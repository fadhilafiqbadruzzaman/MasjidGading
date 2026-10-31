class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? address;
  final String role; // 'super_admin', 'admin_masjid', 'jamaah', 'marbot'
  final bool isActive;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.address,
    required this.role,
    this.isActive = true,
  });

  bool get isAdmin => role == 'admin_masjid' || role == 'super_admin';
  bool get isMarbot => role == 'marbot';
  bool get isJamaah => role == 'jamaah';

  String get roleDisplay {
    switch (role) {
      case 'super_admin':
        return 'Super Admin';
      case 'admin_masjid':
        return 'Admin Masjid';
      case 'marbot':
        return 'Marbot Masjid';
      case 'jamaah':
      default:
        return 'Jamaah';
    }
  }

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      name: json['name'] as String? ?? '',
      email: json['email'] as String? ?? '',
      phone: json['phone'] as String?,
      address: json['address'] as String?,
      role: json['role'] as String? ?? 'jamaah',
      isActive: json['is_active'] == 1 || json['is_active'] == true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'address': address,
      'role': role,
      'is_active': isActive,
    };
  }
}
