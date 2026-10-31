import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../../features/auth/domain/user_model.dart';

class AuthState {
  final UserModel? user;
  final bool isLoading;
  final String? errorMessage;

  AuthState({
    this.user,
    this.isLoading = false,
    this.errorMessage,
  });

  bool get isAuthenticated => user != null;

  AuthState copyWith({
    UserModel? user,
    bool? isLoading,
    String? errorMessage,
    bool clearUser = false,
  }) {
    return AuthState(
      user: clearUser ? null : (user ?? this.user),
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final ApiClient _apiClient = ApiClient();

  AuthNotifier() : super(AuthState()) {
    checkAuth();
  }

  Future<void> checkAuth() async {
    final token = await _apiClient.storage.read(key: ApiConstants.keyToken);
    final userJson = await _apiClient.storage.read(key: ApiConstants.keyUser);

    if (token != null && userJson != null) {
      try {
        final user = UserModel.fromJson(jsonDecode(userJson));
        state = state.copyWith(user: user);

        // Refresh profile from server
        final response = await _apiClient.dio.get('/profile');
        if (response.data['success'] == true) {
          final refreshedUser = UserModel.fromJson(response.data['data']);
          await _apiClient.storage.write(
            key: ApiConstants.keyUser,
            value: jsonEncode(refreshedUser.toJson()),
          );
          state = state.copyWith(user: refreshedUser);
        }
      } catch (_) {
        // Offline or token expired
      }
    }
  }

  Future<bool> login(String email, String password) async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final response = await _apiClient.dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.data['success'] == true) {
        final token = response.data['data']['token'];
        final user = UserModel.fromJson(response.data['data']['user']);

        await _apiClient.storage.write(key: ApiConstants.keyToken, value: token);
        await _apiClient.storage.write(
          key: ApiConstants.keyUser,
          value: jsonEncode(user.toJson()),
        );

        state = state.copyWith(user: user, isLoading: false);
        return true;
      } else {
        state = state.copyWith(
          isLoading: false,
          errorMessage: response.data['message'] ?? 'Login gagal',
        );
        return false;
      }
    } on DioException catch (e) {
      final message = e.response?.data['message'] ?? 'Email atau password salah';
      state = state.copyWith(isLoading: false, errorMessage: message);
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    String? phone,
    String? address,
  }) async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      final response = await _apiClient.dio.post('/register', data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
        'phone': phone,
        'address': address,
      });

      if (response.data['success'] == true) {
        final token = response.data['data']['token'];
        final user = UserModel.fromJson(response.data['data']['user']);

        await _apiClient.storage.write(key: ApiConstants.keyToken, value: token);
        await _apiClient.storage.write(
          key: ApiConstants.keyUser,
          value: jsonEncode(user.toJson()),
        );

        state = state.copyWith(user: user, isLoading: false);
        return true;
      } else {
        state = state.copyWith(
          isLoading: false,
          errorMessage: response.data['message'] ?? 'Registrasi gagal',
        );
        return false;
      }
    } on DioException catch (e) {
      final message = e.response?.data['message'] ?? 'Pendaftaran gagal';
      state = state.copyWith(isLoading: false, errorMessage: message);
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _apiClient.dio.post('/logout');
    } catch (_) {}
    await _apiClient.storage.delete(key: ApiConstants.keyToken);
    await _apiClient.storage.delete(key: ApiConstants.keyUser);
    state = state.copyWith(clearUser: true, isLoading: false);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
