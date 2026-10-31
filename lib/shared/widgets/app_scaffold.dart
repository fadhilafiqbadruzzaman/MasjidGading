import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';

class AppScaffold extends ConsumerWidget {
  final Widget child;

  const AppScaffold({super.key, required this.child});

  int _calculateSelectedIndex(BuildContext context, bool isAdmin) {
    final location = GoRouterState.of(context).uri.toString();
    if (location.startsWith('/announcements')) return 1;
    if (location.startsWith('/events')) return 2;
    if (location.startsWith('/donations')) return 3;
    if (isAdmin && location.startsWith('/admin-hub')) return 4;
    if (!isAdmin && location.startsWith('/profile')) return 4;
    return 0; // Beranda
  }

  void _onItemTapped(int index, BuildContext context, bool isAdmin) {
    switch (index) {
      case 0:
        context.go('/');
        break;
      case 1:
        context.go('/announcements');
        break;
      case 2:
        context.go('/events');
        break;
      case 3:
        context.go('/donations');
        break;
      case 4:
        if (isAdmin) {
          context.push('/admin-hub');
        } else {
          context.go('/profile');
        }
        break;
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);
    final isAdmin = authState.user?.isAdmin == true;
    final selectedIndex = _calculateSelectedIndex(context, isAdmin);

    return Scaffold(
      body: child,
      bottomNavigationBar: NavigationBar(
        selectedIndex: selectedIndex,
        onDestinationSelected: (idx) => _onItemTapped(idx, context, isAdmin),
        indicatorColor: isAdmin && selectedIndex == 4 ? Colors.amber.withOpacity(0.2) : AppTheme.primary.withOpacity(0.15),
        destinations: [
          const NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home, color: AppTheme.primary),
            label: 'Beranda',
          ),
          const NavigationDestination(
            icon: Icon(Icons.campaign_outlined),
            selectedIcon: Icon(Icons.campaign, color: AppTheme.primary),
            label: 'Warta',
          ),
          const NavigationDestination(
            icon: Icon(Icons.event_outlined),
            selectedIcon: Icon(Icons.event, color: AppTheme.primary),
            label: 'Kegiatan',
          ),
          const NavigationDestination(
            icon: Icon(Icons.favorite_outline),
            selectedIcon: Icon(Icons.favorite, color: AppTheme.primary),
            label: 'Donasi',
          ),
          if (isAdmin)
            NavigationDestination(
              icon: const Icon(Icons.admin_panel_settings_outlined),
              selectedIcon: Icon(Icons.admin_panel_settings, color: Colors.amber.shade800),
              label: 'Kelola',
            )
          else
            const NavigationDestination(
              icon: Icon(Icons.person_outline),
              selectedIcon: Icon(Icons.person, color: AppTheme.primary),
              label: 'Profil',
            ),
        ],
      ),
    );
  }
}

