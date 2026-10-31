import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../core/network/api_client.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/currency_formatter.dart';
import '../core/utils/date_formatter.dart';
import '../features/announcement/domain/announcement_model.dart';
import '../features/donation/domain/donation_campaign_model.dart';
import '../shared/providers/auth_provider.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  List<AnnouncementModel> _announcements = [];
  List<DonationCampaignModel> _campaigns = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchHomeData();
  }

  Future<void> _fetchHomeData() async {
    try {
      final annRes = await ApiClient().dio.get('/announcements?per_page=3');
      final donRes = await ApiClient().dio.get('/donation-campaigns?per_page=2');

      if (mounted) {
        setState(() {
          if (annRes.data['success'] == true) {
            final annList = annRes.data['data']['data'] as List? ?? [];
            _announcements = annList.map((e) => AnnouncementModel.fromJson(e)).toList();
          }
          if (donRes.data['success'] == true) {
            final donList = donRes.data['data']['data'] as List? ?? [];
            _campaigns = donList.map((e) => DonationCampaignModel.fromJson(e)).toList();
          }
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final user = authState.user;

    return Scaffold(
      body: RefreshIndicator(
        onRefresh: _fetchHomeData,
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Gradient
              Container(
                width: double.infinity,
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [AppTheme.primaryDark, AppTheme.primary],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(28),
                    bottomRight: Radius.circular(28),
                  ),
                ),
                padding: const EdgeInsets.only(top: 50, left: 20, right: 20, bottom: 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.mosque, color: Colors.white, size: 28),
                            const SizedBox(width: 10),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Masjid BGC',
                                  style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                                ),
                                Text(
                                  'Bukit Gading Cisoka, Tangerang',
                                  style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 12),
                                ),
                              ],
                            ),
                          ],
                        ),
                        if (user != null)
                          GestureDetector(
                            onTap: () => context.push('/profile'),
                            child: CircleAvatar(
                              backgroundColor: Colors.white24,
                              child: Text(
                                user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                              ),
                            ),
                          )
                        else
                          TextButton(
                            onPressed: () => context.push('/login'),
                            style: TextButton.styleFrom(
                              backgroundColor: Colors.white24,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                            ),
                            child: const Text('Masuk'),
                          ),
                      ],
                    ),
                    const SizedBox(height: 20),

                    // Greeting
                    Text(
                      user != null ? 'Assalamu\'alaikum, ${user.name}' : 'Assalamu\'alaikum, Jamaah',
                      style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 12),

                    // Prayer times card
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.12),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: Colors.white.withOpacity(0.2)),
                      ),
                      child: const Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: [
                          _PrayerItem('Subuh', '04:24'),
                          _PrayerItem('Dzuhur', '11:48'),
                          _PrayerItem('Ashar', '14:58'),
                          _PrayerItem('Maghrib', '17:54'),
                          _PrayerItem('Isya', '19:04'),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 20),

              if (user != null && user.isAdmin) ...[
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Colors.amber.shade900, Colors.amber.shade700],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.amber.withOpacity(0.3),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.shield, color: Colors.white, size: 20),
                            const SizedBox(width: 8),
                            Text(
                              user.role == 'super_admin' ? 'AKSES SUPER ADMIN' : 'AKSES ADMIN MASJID',
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13, letterSpacing: 0.5),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        const Text(
                          'Anda memiliki hak akses penuh untuk mengelola (CRUD) warta, kegiatan, donasi, kas, dan verifikasi bansos.',
                          style: TextStyle(color: Colors.white, fontSize: 12, height: 1.4),
                        ),
                        const SizedBox(height: 12),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: Colors.amber.shade900,
                              elevation: 0,
                              padding: const EdgeInsets.symmetric(vertical: 10),
                            ),
                            icon: const Icon(Icons.admin_panel_settings, size: 18),
                            label: const Text('Buka Panel Admin & CRUD', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                            onPressed: () => context.push('/admin-hub'),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
              ],

              // Quick Access Grid
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Layanan Masjid',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                    const SizedBox(height: 14),
                    GridView.count(
                      crossAxisCount: 4,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      mainAxisSpacing: 12,
                      crossAxisSpacing: 8,
                      children: [
                        if (user != null && user.isAdmin)
                          _quickMenu(context, Icons.admin_panel_settings, 'Kelola (CRUD)', '/admin-hub', Colors.amber.shade800),
                        _quickMenu(context, Icons.campaign, 'Pengumuman', '/announcements', Colors.blue),
                        _quickMenu(context, Icons.event, 'Kegiatan', '/events', Colors.indigo),
                        _quickMenu(context, Icons.favorite, 'Donasi', '/donations', Colors.red),
                        _quickMenu(context, Icons.account_balance_wallet, 'Keuangan', '/finance', Colors.teal),
                        _quickMenu(context, Icons.verified_user, 'Jejak Amanah', '/transparency', AppTheme.primary),
                        _quickMenu(context, Icons.volunteer_activism, 'Zakat', '/zakat', Colors.amber.shade800),
                        _quickMenu(context, Icons.pets, 'Qurban', '/qurban', Colors.brown),
                        _quickMenu(context, Icons.handshake, 'Bansos', '/social', Colors.purple),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // Marbot Quick Card (Jika role Marbot atau Admin)
              if (user != null && (user.isMarbot || user.isAdmin))
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                  child: Card(
                    color: Colors.orange.shade50,
                    child: ListTile(
                      leading: const CircleAvatar(
                        backgroundColor: Colors.orange,
                        child: Icon(Icons.build, color: Colors.white),
                      ),
                      title: const Text('Menu Operasional Marbot', style: TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: const Text('Lihat tugas pemeliharaan & lapor fasilitas'),
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () => context.push('/marbot-tasks'),
                    ),
                  ),
                ),

              const SizedBox(height: 16),

              // Latest Announcements Section
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Warta & Pengumuman',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                    TextButton(
                      onPressed: () => context.push('/announcements'),
                      child: const Text('Lihat Semua'),
                    ),
                  ],
                ),
              ),

              if (_isLoading)
                const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()))
              else if (_announcements.isEmpty)
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16),
                  child: Text('Belum ada pengumuman baru.'),
                )
              else
                ..._announcements.map((item) => Card(
                      child: ListTile(
                        title: Text(item.title, style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Text(
                          item.content,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        trailing: Text(
                          DateFormatter.format(item.publishedAt),
                          style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                        ),
                        onTap: () => context.push('/announcements/${item.id}', extra: item),
                      ),
                    )),

              const SizedBox(height: 20),

              // Active Donation Campaigns Section
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Program Infaq Pilihan',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                    TextButton(
                      onPressed: () => context.push('/donations'),
                      child: const Text('Lihat Semua'),
                    ),
                  ],
                ),
              ),

              if (_campaigns.isNotEmpty)
                ..._campaigns.map((camp) => Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(camp.title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                            const SizedBox(height: 8),
                            LinearProgressIndicator(
                              value: (camp.progressPercentage / 100).clamp(0.0, 1.0),
                              backgroundColor: Colors.grey.shade200,
                              color: AppTheme.secondary,
                            ),
                            const SizedBox(height: 8),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Terkumpul: ${CurrencyFormatter.format(camp.collectedAmount)}', style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primary, fontSize: 13)),
                                Text('${camp.progressPercentage.toStringAsFixed(0)}%', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              ],
                            ),
                            const SizedBox(height: 10),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton(
                                onPressed: () => context.push('/donate', extra: camp),
                                child: const Text('Salurkan Donasi'),
                              ),
                            ),
                          ],
                        ),
                      ),
                    )),

              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  Widget _quickMenu(BuildContext context, IconData icon, String title, String route, Color color) {
    return GestureDetector(
      onTap: () => context.push(route),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 6),
          Text(
            title,
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
            textAlign: TextAlign.center,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}

class _PrayerItem extends StatelessWidget {
  final String name;
  final String time;

  const _PrayerItem(this.name, this.time);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(name, style: TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 11)),
        const SizedBox(height: 4),
        Text(time, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
      ],
    );
  }
}
