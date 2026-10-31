class ApiConstants {
  // Gunakan String.fromEnvironment agar fleksibel, default ke IP LAN Windows host melalui reverse proxy
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://192.168.100.10:8008/api',
  );
  static const String storageUrl = String.fromEnvironment(
    'STORAGE_BASE_URL',
    defaultValue: 'http://192.168.100.10:8008/storage',
  );

  static const String keyToken = 'auth_token_key';
  static const String keyUser = 'auth_user_key';
}
