import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../shared/providers/auth_provider.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);
    final user = authState.user;

    if (!authState.isAuthenticated || user == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Profil Pengguna')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.account_circle_outlined, size: 80, color: AppTheme.textMuted),
                const SizedBox(height: 16),
                const Text(
                  'Anda Belum Masuk',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Silakan masuk atau buat akun untuk melihat riwayat donasi dan status akun.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppTheme.textMuted),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () => context.push('/login'),
                  child: const Text('Masuk ke Akun'),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Profil Saya'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Keluar',
            onPressed: () async {
              final confirm = await showDialog<bool>(
                context: context,
                builder: (ctx) => AlertDialog(
                  title: const Text('Konfirmasi Logout'),
                  content: const Text('Apakah Anda yakin ingin keluar dari akun ini?'),
                  actions: [
                    TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
                    ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Keluar')),
                  ],
                ),
              );
              if (confirm == true) {
                await ref.read(authProvider.notifier).logout();
                if (context.mounted) {
                  context.go('/');
                }
              }
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header
            Container(
              width: double.infinity,
              color: AppTheme.primary,
              padding: const EdgeInsets.only(bottom: 28, left: 16, right: 16),
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 40,
                    backgroundColor: Colors.white,
                    child: Text(
                      user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                      style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: AppTheme.primary),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    user.name,
                    style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    user.email,
                    style: const TextStyle(color: Colors.white70, fontSize: 14),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                    decoration: BoxDecoration(
                      color: user.isMarbot
                          ? Colors.orangeAccent
                          : (user.isAdmin ? AppTheme.accentGold : Colors.white24),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      user.roleDisplay,
                      style: TextStyle(
                        color: user.isMarbot || user.isAdmin ? Colors.black87 : Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Profile info items
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.phone_outlined, color: AppTheme.primary),
                    title: const Text('Nomor Telepon / WhatsApp'),
                    subtitle: Text(user.phone ?? 'Belum diisi'),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.home_outlined, color: AppTheme.primary),
                    title: const Text('Alamat Domisili'),
                    subtitle: Text(user.address ?? 'Belum diisi'),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.verified_user_outlined, color: AppTheme.primary),
                    title: const Text('Status Akun'),
                    subtitle: Text(user.isActive ? 'Aktif' : 'Dinonaktifkan'),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 10),

            // Navigation shortcuts
            Card(
              child: Column(
                children: [
                  if (user.isAdmin) ...[
                    ListTile(
                      leading: const Icon(Icons.admin_panel_settings, color: Colors.amber, size: 28),
                      title: const Text('Panel Pengurus & CRUD Masjid', style: TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primary)),
                      subtitle: const Text('Kelola warta, kegiatan, donasi, kas, dan verifikasi bansos'),
                      trailing: const Icon(Icons.chevron_right, color: AppTheme.primary),
                      onTap: () => context.push('/admin-hub'),
                    ),
                    const Divider(height: 1),
                  ],
                  ListTile(
                    leading: const Icon(Icons.favorite_outline, color: AppTheme.secondary),
                    title: const Text('Riwayat Donasi Saya'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/my-donations'),
                  ),
                  if (user.isMarbot || user.isAdmin) ...[
                    const Divider(height: 1),
                    ListTile(
                      leading: const Icon(Icons.build_circle_outlined, color: Colors.orange),
                      title: const Text('Tugas Pemeliharaan (Marbot)'),
                      subtitle: const Text('Kelola tugas operasional masjid'),
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () => context.push('/marbot-tasks'),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
