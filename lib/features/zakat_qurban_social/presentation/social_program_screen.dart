import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/date_formatter.dart';
import '../../../shared/providers/auth_provider.dart';

class SocialProgramScreen extends ConsumerStatefulWidget {
  const SocialProgramScreen({super.key});

  @override
  ConsumerState<SocialProgramScreen> createState() => _SocialProgramScreenState();
}

class _SocialProgramScreenState extends ConsumerState<SocialProgramScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  List<dynamic> _programs = [];
  List<dynamic> _myApplications = [];
  bool _isLoadingPrograms = true;
  bool _isLoadingMyApps = false;
  String? _programError;
  String? _myAppsError;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _fetchSocialPrograms();
    _fetchMyApplications();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _fetchSocialPrograms() async {
    setState(() {
      _isLoadingPrograms = true;
      _programError = null;
    });
    try {
      final res = await ApiClient().dio.get('/social-programs');
      if (res.data['success'] == true) {
        setState(() {
          _programs = res.data['data'] as List? ?? [];
          _isLoadingPrograms = false;
        });
      } else {
        setState(() {
          _isLoadingPrograms = false;
          _programError = 'Gagal memuat program bantuan sosial.';
        });
      }
    } catch (e) {
      setState(() {
        _isLoadingPrograms = false;
        _programError = 'Gagal memuat program bantuan sosial. Periksa koneksi.';
      });
    }
  }

  Future<void> _fetchMyApplications() async {
    final authState = ref.read(authProvider);
    if (!authState.isAuthenticated) return;

    setState(() {
      _isLoadingMyApps = true;
      _myAppsError = null;
    });

    try {
      final res = await ApiClient().dio.get('/my-social-applications');
      if (res.data['success'] == true) {
        setState(() {
          _myApplications = res.data['data'] as List? ?? [];
          _isLoadingMyApps = false;
        });
      } else {
        setState(() {
          _isLoadingMyApps = false;
          _myAppsError = 'Gagal memuat riwayat pengajuan.';
        });
      }
    } catch (e) {
      setState(() {
        _isLoadingMyApps = false;
        _myAppsError = 'Gagal memuat riwayat pengajuan.';
      });
    }
  }

  void _showApplyDialog(dynamic program) {
    final authState = ref.read(authProvider);

    if (!authState.isAuthenticated) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan masuk (login) terlebih dahulu untuk mengajukan permohonan bantuan.'),
          backgroundColor: Colors.orange,
        ),
      );
      context.push('/login');
      return;
    }

    final reasonController = TextEditingController();
    final amountController = TextEditingController();
    final phoneController = TextEditingController(text: authState.user?.phone ?? '');
    final addressController = TextEditingController(text: authState.user?.address ?? '');

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Permohonan: ${program['title']}'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Data pribadi pemohon dijaga kerahasiaannya dan hanya diverifikasi oleh pengurus berwenang.',
                style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: phoneController,
                keyboardType: TextInputType.phone,
                decoration: const InputDecoration(labelText: 'No. HP / WA Aktif'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: addressController,
                decoration: const InputDecoration(labelText: 'Alamat Tempat Tinggal'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: amountController,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(
                  labelText: 'Estimasi Kebutuhan Biaya (Rp)',
                  hintText: 'Contoh: 1000000',
                ),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: reasonController,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Alasan / Kondisi yang Dihadapi',
                  hintText: 'Jelaskan kondisi mendesak kebutuhan...',
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () async {
              if (reasonController.text.trim().isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Alasan permohonan wajib diisi.'), backgroundColor: Colors.red),
                );
                return;
              }
              Navigator.pop(ctx);
              try {
                final cleanAmount = amountController.text.replaceAll(RegExp(r'[^0-9]'), '');
                final res = await ApiClient().dio.post('/social-programs/${program['id']}/apply', data: {
                  'applicant_name': authState.user?.name ?? 'Jamaah',
                  'phone': phoneController.text.trim(),
                  'address': addressController.text.trim(),
                  'reason': reasonController.text.trim(),
                  'requested_amount': double.tryParse(cleanAmount) ?? 0,
                });

                if (mounted) {
                  if (res.data['success'] == true) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Alhamdulillah! Permohonan bantuan Anda telah dikirim dan tercatat dalam sistem.'),
                        backgroundColor: Colors.green,
                      ),
                    );
                    await _fetchMyApplications();
                    _tabController.animateTo(1);
                  }
                }
              } catch (_) {
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Gagal mengirim permohonan. Periksa koneksi.'), backgroundColor: Colors.red),
                  );
                }
              }
            },
            child: const Text('Kirim Pengajuan'),
          ),
        ],
      ),
    );
  }

  Widget _buildProgramsList() {
    return RefreshIndicator(
      onRefresh: _fetchSocialPrograms,
      child: _isLoadingPrograms
          ? const Center(child: CircularProgressIndicator())
          : _programError != null
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error_outline, size: 48, color: Colors.orange),
                        const SizedBox(height: 12),
                        Text(_programError!, textAlign: TextAlign.center),
                        const SizedBox(height: 16),
                        ElevatedButton(onPressed: _fetchSocialPrograms, child: const Text('Coba Lagi')),
                      ],
                    ),
                  ),
                )
              : _programs.isEmpty
                  ? const Center(child: Text('Belum ada program bantuan sosial aktif.'))
                  : ListView.builder(
                      padding: const EdgeInsets.all(12),
                      itemCount: _programs.length,
                      itemBuilder: (context, index) {
                        final p = _programs[index];
                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          child: Padding(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  p['title'] ?? '',
                                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primary),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  p['description'] ?? '',
                                  style: const TextStyle(fontSize: 13, color: AppTheme.textDark, height: 1.5),
                                ),
                                if (p['budget'] != null) ...[
                                  const SizedBox(height: 10),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                    decoration: BoxDecoration(
                                      color: Colors.blue.shade50,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(color: Colors.blue.shade200),
                                    ),
                                    child: Text(
                                      'Alokasi Anggaran: ${CurrencyFormatter.format(p['budget'])}',
                                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blue.shade800),
                                    ),
                                  ),
                                ],
                                const SizedBox(height: 14),
                                SizedBox(
                                  width: double.infinity,
                                  child: ElevatedButton.icon(
                                    icon: const Icon(Icons.send, size: 16),
                                    label: const Text('Ajukan Permohonan Bantuan'),
                                    onPressed: () => _showApplyDialog(p),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
    );
  }

  Widget _buildMyApplicationsList() {
    final authState = ref.watch(authProvider);

    if (!authState.isAuthenticated) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.lock_outline, size: 56, color: AppTheme.textMuted),
              const SizedBox(height: 16),
              const Text(
                'Masuk untuk Melihat Pengajuan',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              const Text(
                'Anda harus login sebagai Jamaah untuk melihat status permohonan bansos yang pernah Anda ajukan.',
                textAlign: TextAlign.center,
                style: TextStyle(color: AppTheme.textMuted, fontSize: 13),
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () => context.push('/login'),
                child: const Text('Masuk ke Akun'),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _fetchMyApplications,
      child: _isLoadingMyApps
          ? const Center(child: CircularProgressIndicator())
          : _myAppsError != null
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error_outline, size: 48, color: Colors.orange),
                        const SizedBox(height: 12),
                        Text(_myAppsError!, textAlign: TextAlign.center),
                        const SizedBox(height: 16),
                        ElevatedButton(onPressed: _fetchMyApplications, child: const Text('Coba Lagi')),
                      ],
                    ),
                  ),
                )
              : _myApplications.isEmpty
                  ? Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.inbox_outlined, size: 56, color: AppTheme.textMuted),
                            const SizedBox(height: 16),
                            const Text(
                              'Belum Ada Pengajuan',
                              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Anda belum pernah mengajukan permohonan bantuan sosial. Silakan pilih program pada tab "Program Bantuan".',
                              textAlign: TextAlign.center,
                              style: TextStyle(color: AppTheme.textMuted, fontSize: 13),
                            ),
                            const SizedBox(height: 20),
                            OutlinedButton(
                              onPressed: () => _tabController.animateTo(0),
                              child: const Text('Lihat Program Bansos'),
                            ),
                          ],
                        ),
                      ),
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(12),
                      itemCount: _myApplications.length,
                      itemBuilder: (context, index) {
                        final item = _myApplications[index];
                        final status = item['status'] as String? ?? 'pending';
                        final programTitle = item['program']?['title'] ?? 'Program Bantuan Sosial';
                        final requestedAmount = item['requested_amount'];
                        final reason = item['reason'] ?? '';
                        final notes = item['notes'];
                        final createdAt = item['created_at'];

                        Color statusColor;
                        Color statusBgColor;
                        String statusLabel;

                        switch (status) {
                          case 'approved':
                            statusColor = Colors.green.shade800;
                            statusBgColor = Colors.green.shade50;
                            statusLabel = 'Disetujui Pengurus';
                            break;
                          case 'rejected':
                            statusColor = Colors.red.shade800;
                            statusBgColor = Colors.red.shade50;
                            statusLabel = 'Ditolak';
                            break;
                          case 'distributed':
                            statusColor = Colors.blue.shade800;
                            statusBgColor = Colors.blue.shade50;
                            statusLabel = 'Telah Disalurkan';
                            break;
                          case 'pending':
                          default:
                            statusColor = Colors.orange.shade800;
                            statusBgColor = Colors.orange.shade50;
                            statusLabel = 'Menunggu Review';
                            break;
                        }

                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          child: Padding(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        programTitle,
                                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.primary),
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: statusBgColor,
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(color: statusColor.withOpacity(0.3)),
                                      ),
                                      child: Text(
                                        statusLabel,
                                        style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 11),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                if (requestedAmount != null) ...[
                                  Row(
                                    children: [
                                      const Text('Kebutuhan Biaya: ', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                                      Text(
                                        CurrencyFormatter.format(requestedAmount),
                                        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.black87),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 6),
                                ],
                                Text(
                                  'Alasan: $reason',
                                  style: const TextStyle(fontSize: 13, color: AppTheme.textDark, height: 1.4),
                                ),
                                if (notes != null && notes.toString().trim().isNotEmpty) ...[
                                  const SizedBox(height: 10),
                                  Container(
                                    width: double.infinity,
                                    padding: const EdgeInsets.all(10),
                                    decoration: BoxDecoration(
                                      color: Colors.amber.shade50,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(color: Colors.amber.shade200),
                                    ),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text(
                                          'Catatan Verifikasi Pengurus:',
                                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.brown),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(notes.toString(), style: const TextStyle(fontSize: 12, color: Colors.brown)),
                                      ],
                                    ),
                                  ),
                                ],
                                const SizedBox(height: 10),
                                const Divider(height: 1),
                                const SizedBox(height: 8),
                                Text(
                                  'Diajukan pada: ${DateFormatter.formatWithTime(createdAt)}',
                                  style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Program Bantuan Sosial'),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          indicatorWeight: 3,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(
              icon: Icon(Icons.volunteer_activism, size: 20),
              text: 'Program Tersedia',
            ),
            Tab(
              icon: Icon(Icons.history, size: 20),
              text: 'Pengajuan Saya',
            ),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildProgramsList(),
          _buildMyApplicationsList(),
        ],
      ),
    );
  }
}
