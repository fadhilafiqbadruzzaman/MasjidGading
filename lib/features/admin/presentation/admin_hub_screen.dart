import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/date_formatter.dart';
import '../../../shared/providers/auth_provider.dart';

class AdminHubScreen extends ConsumerStatefulWidget {
  const AdminHubScreen({super.key});

  @override
  ConsumerState<AdminHubScreen> createState() => _AdminHubScreenState();
}

class _AdminHubScreenState extends ConsumerState<AdminHubScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  // Data lists
  List<dynamic> _announcements = [];
  List<dynamic> _events = [];
  List<dynamic> _campaigns = [];
  List<dynamic> _socialApplications = [];
  List<dynamic> _tasks = [];
  List<dynamic> _transactions = [];

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _refreshAllData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _refreshAllData() async {
    setState(() => _isLoading = true);
    try {
      final dio = ApiClient().dio;

      final results = await Future.wait([
        dio.get('/announcements').catchError((_) => dio.get('/announcements')),
        dio.get('/events').catchError((_) => dio.get('/events')),
        dio.get('/donation-campaigns').catchError((_) => dio.get('/donation-campaigns')),
        dio.get('/admin/social-applications').catchError((_) => dio.get('/admin/social-applications')),
        dio.get('/admin/tasks').catchError((_) => dio.get('/admin/tasks')),
        dio.get('/admin/transactions').catchError((_) => dio.get('/admin/transactions')),
      ]);

      if (mounted) {
        setState(() {
          _announcements = results[0].data['data']?['data'] as List? ?? results[0].data['data'] as List? ?? [];
          _events = results[1].data['data']?['data'] as List? ?? results[1].data['data'] as List? ?? [];
          _campaigns = results[2].data['data']?['data'] as List? ?? results[2].data['data'] as List? ?? [];
          _socialApplications = results[3].data['data'] as List? ?? [];
          _tasks = results[4].data['data']?['data'] as List? ?? results[4].data['data'] as List? ?? [];
          _transactions = results[5].data['data']?['data'] as List? ?? results[5].data['data'] as List? ?? [];
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  // -------------------------------------------------------------
  // CRUD WARTA / PENGUMUMAN
  // -------------------------------------------------------------
  void _showAddAnnouncementDialog() {
    final messenger = ScaffoldMessenger.of(context);
    final titleCtrl = TextEditingController();
    final contentCtrl = TextEditingController();
    String status = 'published';

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDlgState) => AlertDialog(
          title: const Text('Buat Warta / Pengumuman'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: titleCtrl,
                  decoration: const InputDecoration(labelText: 'Judul Warta *'),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: contentCtrl,
                  maxLines: 4,
                  decoration: const InputDecoration(labelText: 'Isi Pengumuman *'),
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  value: status,
                  decoration: const InputDecoration(labelText: 'Status Publikasi'),
                  items: const [
                    DropdownMenuItem(value: 'published', child: Text('Langsung Terbitkan (Tayang)')),
                    DropdownMenuItem(value: 'draft', child: Text('Simpan sebagai Draf')),
                  ],
                  onChanged: (v) => setDlgState(() => status = v ?? 'published'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                if (titleCtrl.text.trim().isEmpty || contentCtrl.text.trim().isEmpty) return;
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.post('/admin/announcements', data: {
                    'title': titleCtrl.text.trim(),
                    'content': contentCtrl.text.trim(),
                    'status': status,
                    'published_at': status == 'published' ? DateTime.now().toIso8601String() : null,
                  });
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Warta pengumuman berhasil disimpan.'), backgroundColor: Colors.green),
                    );
                    _refreshAllData();
                  }
                } catch (_) {
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Gagal menyimpan warta.'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Simpan Warta'),
            ),
          ],
        ),
      ),
    );
  }

  void _deleteAnnouncement(int id) async {
    final messenger = ScaffoldMessenger.of(context);
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Hapus Warta'),
        content: const Text('Apakah Anda yakin ingin menghapus warta pengumuman ini?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await ApiClient().dio.delete('/admin/announcements/$id');
        if (mounted) {
          messenger.showSnackBar(
            const SnackBar(content: Text('Warta berhasil dihapus.'), backgroundColor: Colors.green),
          );
          _refreshAllData();
        }
      } catch (_) {
        if (mounted) {
          messenger.showSnackBar(
            const SnackBar(content: Text('Gagal menghapus warta.'), backgroundColor: Colors.red),
          );
        }
      }
    }
  }

  // -------------------------------------------------------------
  // CRUD KEGIATAN & LIHAT PENDAFTAR
  // -------------------------------------------------------------
  void _showAddEventDialog() {
    final messenger = ScaffoldMessenger.of(context);
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    final locationCtrl = TextEditingController(text: 'Masjid Bukit Gading Cisoka');
    final organizerCtrl = TextEditingController(text: 'DKM Masjid BGC');
    final capacityCtrl = TextEditingController(text: '100');
    final dateCtrl = TextEditingController(text: DateTime.now().toString().substring(0, 10));
    final timeCtrl = TextEditingController(text: '08:30');
    bool isOpen = true;

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDlgState) => AlertDialog(
          title: const Text('Buat Kegiatan / Kajian'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: titleCtrl, decoration: const InputDecoration(labelText: 'Nama Kegiatan *')),
                const SizedBox(height: 10),
                TextField(controller: descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Deskripsi Kegiatan *')),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Expanded(child: TextField(controller: dateCtrl, decoration: const InputDecoration(labelText: 'Tanggal (YYYY-MM-DD)'))),
                    const SizedBox(width: 8),
                    Expanded(child: TextField(controller: timeCtrl, decoration: const InputDecoration(labelText: 'Jam (HH:MM)'))),
                  ],
                ),
                const SizedBox(height: 10),
                TextField(controller: locationCtrl, decoration: const InputDecoration(labelText: 'Lokasi *')),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Expanded(child: TextField(controller: capacityCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Kapasitas Kuota'))),
                    const SizedBox(width: 8),
                    Expanded(child: TextField(controller: organizerCtrl, decoration: const InputDecoration(labelText: 'Penyelenggara'))),
                  ],
                ),
                const SizedBox(height: 10),
                CheckboxListTile(
                  contentPadding: EdgeInsets.zero,
                  title: const Text('Buka Pendaftaran untuk Jamaah', style: TextStyle(fontSize: 13)),
                  value: isOpen,
                  onChanged: (v) => setDlgState(() => isOpen = v ?? true),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                if (titleCtrl.text.trim().isEmpty) return;
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.post('/admin/events', data: {
                    'title': titleCtrl.text.trim(),
                    'description': descCtrl.text.trim(),
                    'location': locationCtrl.text.trim(),
                    'event_date': dateCtrl.text.trim(),
                    'event_time': '${timeCtrl.text.trim()}:00',
                    'capacity': int.tryParse(capacityCtrl.text) ?? 100,
                    'organizer': organizerCtrl.text.trim(),
                    'is_registration_open': isOpen,
                    'status': 'planned',
                  });
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Kegiatan berhasil dibuat.'), backgroundColor: Colors.green),
                    );
                    _refreshAllData();
                  }
                } catch (_) {
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Gagal membuat kegiatan.'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Simpan Kegiatan'),
            ),
          ],
        ),
      ),
    );
  }

  void _showEventParticipants(dynamic event) async {
    final messenger = ScaffoldMessenger.of(context);
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => const Center(child: CircularProgressIndicator()),
    );

    try {
      final res = await ApiClient().dio.get('/admin/events/${event['id']}/participants');
      if (mounted) Navigator.pop(context); // close loader

      final participants = res.data['data']?['participants'] as List? ?? [];

      if (!mounted) return;

      showModalBottomSheet(
        context: context,
        isScrollControlled: true,
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
        builder: (ctx) => DraggableScrollableSheet(
          initialChildSize: 0.7,
          minChildSize: 0.4,
          maxChildSize: 0.95,
          expand: false,
          builder: (_, controller) => Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2))),
                ),
                const SizedBox(height: 14),
                const Text(
                  'Daftar Pendaftar Kegiatan',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primary),
                ),
                Text(
                  event['title'] ?? '',
                  style: const TextStyle(fontSize: 14, color: AppTheme.textMuted),
                ),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(8)),
                  child: Text(
                    'Total Pendaftar: ${participants.length} Jamaah',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.green.shade800),
                  ),
                ),
                const Divider(height: 20),
                Expanded(
                  child: participants.isEmpty
                      ? const Center(child: Text('Belum ada jamaah yang mendaftar.'))
                      : ListView.separated(
                          controller: controller,
                          itemCount: participants.length,
                          separatorBuilder: (_, __) => const Divider(height: 1),
                          itemBuilder: (context, idx) {
                            final p = participants[idx];
                            final user = p['user'] ?? {};
                            return ListTile(
                              leading: CircleAvatar(
                                backgroundColor: AppTheme.primary,
                                child: Text('${idx + 1}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                              ),
                              title: Text(user['name'] ?? 'Jamaah', style: const TextStyle(fontWeight: FontWeight.bold)),
                              subtitle: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('Email: ${user['email'] ?? '-'}', style: const TextStyle(fontSize: 12)),
                                  Text('No. HP / WA: ${user['phone'] ?? '-'}', style: const TextStyle(fontSize: 12, color: AppTheme.primary)),
                                  Text('Waktu Daftar: ${DateFormatter.formatWithTime(p['registered_at'])}', style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                ],
                              ),
                              trailing: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(color: Colors.green.shade100, borderRadius: BorderRadius.circular(8)),
                                child: const Text('Terdaftar', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.green)),
                              ),
                            );
                          },
                        ),
                ),
              ],
            ),
          ),
        ),
      );
    } catch (_) {
      if (mounted) {
        Navigator.pop(context); // close loader
        messenger.showSnackBar(
          const SnackBar(content: Text('Gagal memuat data pendaftar.'), backgroundColor: Colors.red),
        );
      }
    }
  }

  void _deleteEvent(int id) async {
    final messenger = ScaffoldMessenger.of(context);
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Hapus Kegiatan'),
        content: const Text('Apakah Anda yakin ingin menghapus jadwal kegiatan ini?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: Colors.red), onPressed: () => Navigator.pop(ctx, true), child: const Text('Hapus')),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await ApiClient().dio.delete('/admin/events/$id');
        if (mounted) {
          messenger.showSnackBar(
            const SnackBar(content: Text('Kegiatan berhasil dihapus.'), backgroundColor: Colors.green),
          );
          _refreshAllData();
        }
      } catch (_) {
        if (mounted) {
          messenger.showSnackBar(
            const SnackBar(content: Text('Gagal menghapus kegiatan.'), backgroundColor: Colors.red),
          );
        }
      }
    }
  }

  // -------------------------------------------------------------
  // CRUD DONASI & VERIFIKASI BANSOS
  // -------------------------------------------------------------
  void _showAddCampaignDialog() {
    final messenger = ScaffoldMessenger.of(context);
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    final targetCtrl = TextEditingController(text: '10000000');

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Buat Kampanye Donasi & Infaq'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(controller: titleCtrl, decoration: const InputDecoration(labelText: 'Nama Kampanye *')),
              const SizedBox(height: 10),
              TextField(controller: descCtrl, maxLines: 3, decoration: const InputDecoration(labelText: 'Deskripsi Kebutuhan *')),
              const SizedBox(height: 10),
              TextField(controller: targetCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Target Dana (Rp) *')),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () async {
              if (titleCtrl.text.trim().isEmpty) return;
              Navigator.pop(ctx);
              try {
                final target = double.tryParse(targetCtrl.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
                await ApiClient().dio.post('/admin/donation-campaigns', data: {
                  'title': titleCtrl.text.trim(),
                  'description': descCtrl.text.trim(),
                  'target_amount': target,
                  'start_date': DateTime.now().toIso8601String().substring(0, 10),
                  'status': 'active',
                });
                if (mounted) {
                  messenger.showSnackBar(
                    const SnackBar(content: Text('Kampanye donasi berhasil ditambahkan.'), backgroundColor: Colors.green),
                  );
                  _refreshAllData();
                }
              } catch (_) {
                if (mounted) {
                  messenger.showSnackBar(
                    const SnackBar(content: Text('Gagal membuat kampanye donasi.'), backgroundColor: Colors.red),
                  );
                }
              }
            },
            child: const Text('Simpan Donasi'),
          ),
        ],
      ),
    );
  }

  void _showReviewBansosDialog(dynamic app) {
    final messenger = ScaffoldMessenger.of(context);
    String status = 'approved';
    final notesCtrl = TextEditingController(text: app['notes'] ?? '');

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDlgState) => AlertDialog(
          title: Text('Verifikasi: ${app['applicant_name']}'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Program: ${app['program']?['title'] ?? '-'}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                Text('Nominal: ${CurrencyFormatter.format(app['requested_amount'])}', style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.bold)),
                Text('Alasan: ${app['reason']}', style: const TextStyle(fontSize: 12, color: AppTheme.textDark)),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  value: status,
                  decoration: const InputDecoration(labelText: 'Keputusan Verifikasi *'),
                  items: const [
                    DropdownMenuItem(value: 'approved', child: Text('Setujui Permohonan')),
                    DropdownMenuItem(value: 'rejected', child: Text('Tolak Permohonan')),
                    DropdownMenuItem(value: 'distributed', child: Text('Sudah Disalurkan')),
                  ],
                  onChanged: (v) => setDlgState(() => status = v ?? 'approved'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: notesCtrl,
                  maxLines: 3,
                  decoration: const InputDecoration(labelText: 'Catatan Hasil Survei / Alasan Pengurus'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.post('/admin/social-applications/${app['id']}/review', data: {
                    'status': status,
                    'notes': notesCtrl.text.trim(),
                  });
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Status permohonan bansos berhasil diperbarui.'), backgroundColor: Colors.green),
                    );
                    _refreshAllData();
                  }
                } catch (_) {
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Gagal memperbarui status permohonan.'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Simpan Verifikasi'),
            ),
          ],
        ),
      ),
    );
  }

  // -------------------------------------------------------------
  // CRUD KAS & TUGAS MARBOT
  // -------------------------------------------------------------
  void _showAddTransactionDialog() {
    final messenger = ScaffoldMessenger.of(context);
    String type = 'income';
    final amountCtrl = TextEditingController(text: '100000');
    final descCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDlgState) => AlertDialog(
          title: const Text('Catat Transaksi Kas'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<String>(
                  value: type,
                  decoration: const InputDecoration(labelText: 'Jenis Transaksi *'),
                  items: const [
                    DropdownMenuItem(value: 'income', child: Text('Pemasukan (Infaq / Sedekah)')),
                    DropdownMenuItem(value: 'expense', child: Text('Pengeluaran (Operasional / Perbaikan)')),
                  ],
                  onChanged: (v) => setDlgState(() => type = v ?? 'income'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: amountCtrl,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Nominal (Rp) *'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: descCtrl,
                  maxLines: 2,
                  decoration: const InputDecoration(labelText: 'Keterangan Transaksi *', hintText: 'Misal: Pembelian lampu penerangan masjid'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                final amount = double.tryParse(amountCtrl.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
                if (amount <= 0 || descCtrl.text.trim().isEmpty) return;
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.post('/admin/transactions', data: {
                    'type': type,
                    'category_id': 1,
                    'amount': amount,
                    'description': descCtrl.text.trim(),
                    'transaction_date': DateTime.now().toIso8601String().substring(0, 10),
                    'status': 'verified',
                  });
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Transaksi kas berhasil dicatat dan diverifikasi.'), backgroundColor: Colors.green),
                    );
                    _refreshAllData();
                  }
                } catch (_) {
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Gagal mencatat transaksi kas.'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Catat Transaksi'),
            ),
          ],
        ),
      ),
    );
  }

  void _showAddTaskDialog() {
    final messenger = ScaffoldMessenger.of(context);
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    final locCtrl = TextEditingController(text: 'Area Utama Masjid BGC');
    String priority = 'medium';

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDlgState) => AlertDialog(
          title: const Text('Beri Tugas ke Marbot'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: titleCtrl, decoration: const InputDecoration(labelText: 'Nama Tugas *')),
                const SizedBox(height: 10),
                TextField(controller: descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Instruksi Tugas *')),
                const SizedBox(height: 10),
                TextField(controller: locCtrl, decoration: const InputDecoration(labelText: 'Lokasi *')),
                const SizedBox(height: 10),
                DropdownButtonFormField<String>(
                  value: priority,
                  decoration: const InputDecoration(labelText: 'Tingkat Prioritas'),
                  items: const [
                    DropdownMenuItem(value: 'low', child: Text('Rendah')),
                    DropdownMenuItem(value: 'medium', child: Text('Sedang')),
                    DropdownMenuItem(value: 'high', child: Text('Tinggi')),
                    DropdownMenuItem(value: 'urgent', child: Text('Darurat / Segera')),
                  ],
                  onChanged: (v) => setDlgState(() => priority = v ?? 'medium'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                if (titleCtrl.text.trim().isEmpty) return;
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.post('/admin/tasks', data: {
                    'title': titleCtrl.text.trim(),
                    'description': descCtrl.text.trim(),
                    'location': locCtrl.text.trim(),
                    'priority': priority,
                    'status': 'pending',
                    'assigned_to': 3, // marbot user ID default
                  });
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Tugas berhasil diberikan ke Marbot.'), backgroundColor: Colors.green),
                    );
                    _refreshAllData();
                  }
                } catch (_) {
                  if (mounted) {
                    messenger.showSnackBar(
                      const SnackBar(content: Text('Gagal memberikan tugas.'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Kirim Tugas'),
            ),
          ],
        ),
      ),
    );
  }

  // -------------------------------------------------------------
  // BUILDERS
  // -------------------------------------------------------------
  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final user = authState.user;

    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            const Icon(Icons.admin_panel_settings, color: Colors.amberAccent, size: 24),
            const SizedBox(width: 8),
            Text(user?.role == 'super_admin' ? 'Panel Super Admin' : 'Panel Admin Masjid'),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Segarkan Data',
            onPressed: _refreshAllData,
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          indicatorColor: Colors.amberAccent,
          indicatorWeight: 3,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(icon: Icon(Icons.campaign, size: 18), text: 'Warta'),
            Tab(icon: Icon(Icons.event, size: 18), text: 'Kegiatan'),
            Tab(icon: Icon(Icons.volunteer_activism, size: 18), text: 'Donasi & Bansos'),
            Tab(icon: Icon(Icons.account_balance_wallet, size: 18), text: 'Kas & Marbot'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabController,
              children: [
                _buildAnnouncementsTab(),
                _buildEventsTab(),
                _buildDonationsAndBansosTab(),
                _buildFinanceAndMarbotTab(),
              ],
            ),
    );
  }

  // Tab 1: Warta
  Widget _buildAnnouncementsTab() {
    return Scaffold(
      floatingActionButton: FloatingActionButton.extended(
        heroTag: 'fab_announcement',
        onPressed: _showAddAnnouncementDialog,
        icon: const Icon(Icons.add),
        label: const Text('Buat Warta'),
        backgroundColor: AppTheme.primary,
      ),
      body: RefreshIndicator(
        onRefresh: _refreshAllData,
        child: _announcements.isEmpty
            ? const Center(child: Text('Belum ada data warta.'))
            : ListView.builder(
                padding: const EdgeInsets.all(12),
                itemCount: _announcements.length,
                itemBuilder: (context, idx) {
                  final item = _announcements[idx];
                  return Card(
                    child: ListTile(
                      title: Text(item['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text(item['content'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis),
                          const SizedBox(height: 6),
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: item['status'] == 'published' ? Colors.green.shade50 : Colors.grey.shade100,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  item['status'] == 'published' ? 'Tayang' : 'Draf',
                                  style: TextStyle(
                                    fontSize: 10,
                                    fontWeight: FontWeight.bold,
                                    color: item['status'] == 'published' ? Colors.green.shade800 : Colors.grey.shade600,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(DateFormatter.format(item['published_at'] ?? item['created_at']), style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                            ],
                          ),
                        ],
                      ),
                      trailing: IconButton(
                        icon: const Icon(Icons.delete_outline, color: Colors.red),
                        onPressed: () => _deleteAnnouncement(item['id']),
                      ),
                    ),
                  );
                },
              ),
      ),
    );
  }

  // Tab 2: Kegiatan
  Widget _buildEventsTab() {
    return Scaffold(
      floatingActionButton: FloatingActionButton.extended(
        heroTag: 'fab_event',
        onPressed: _showAddEventDialog,
        icon: const Icon(Icons.add),
        label: const Text('Buat Kegiatan'),
        backgroundColor: AppTheme.primary,
      ),
      body: RefreshIndicator(
        onRefresh: _refreshAllData,
        child: _events.isEmpty
            ? const Center(child: Text('Belum ada jadwal kegiatan.'))
            : ListView.builder(
                padding: const EdgeInsets.all(12),
                itemCount: _events.length,
                itemBuilder: (context, idx) {
                  final event = _events[idx];
                  final regCount = event['active_registrations_count'] ?? event['registrations_count'] ?? 0;
                  final capacity = event['capacity'] ?? 0;

                  return Card(
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Text(event['title'] ?? '', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.primary)),
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
                                onPressed: () => _deleteEvent(event['id']),
                              ),
                            ],
                          ),
                          Text('${DateFormatter.format(event['event_date'])} • ${event['event_time']}', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                          const SizedBox(height: 8),
                          Row(
                            children: [
                              const Icon(Icons.place, size: 14, color: AppTheme.textMuted),
                              const SizedBox(width: 4),
                              Expanded(child: Text(event['location'] ?? '', style: const TextStyle(fontSize: 12))),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(6)),
                                child: Text(
                                  'Pendaftar: $regCount / $capacity Kuota',
                                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blue.shade800),
                                ),
                              ),
                              ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                  textStyle: const TextStyle(fontSize: 12),
                                ),
                                icon: const Icon(Icons.people, size: 16),
                                label: const Text('Lihat Pendaftar'),
                                onPressed: () => _showEventParticipants(event),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
      ),
    );
  }

  // Tab 3: Donasi & Bansos
  Widget _buildDonationsAndBansosTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section 1: Kampanye Donasi
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Program Donasi & Infaq', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ElevatedButton.icon(
                icon: const Icon(Icons.add, size: 16),
                label: const Text('Tambah Donasi'),
                onPressed: _showAddCampaignDialog,
              ),
            ],
          ),
          const SizedBox(height: 8),
          ..._campaigns.map((camp) => Card(
                child: ListTile(
                  title: Text(camp['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                  subtitle: Text(
                    'Terkumpul: ${CurrencyFormatter.format(camp['collected_amount'])} / Target: ${CurrencyFormatter.format(camp['target_amount'])}',
                    style: const TextStyle(fontSize: 12),
                  ),
                  trailing: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(4)),
                    child: Text(camp['status'] ?? 'active', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.green.shade800)),
                  ),
                ),
              )),
          const SizedBox(height: 24),

          // Section 2: Pengajuan Bansos Jamaah
          const Text('Verifikasi Permohonan Bansos Jamaah', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          if (_socialApplications.isEmpty)
            const Card(child: Padding(padding: EdgeInsets.all(16), child: Center(child: Text('Belum ada permohonan bansos masuk.'))))
          else
            ..._socialApplications.map((app) {
              final status = app['status'] ?? 'pending';
              Color statusColor = Colors.orange;
              String statusLabel = 'Menunggu';
              if (status == 'approved') {
                statusColor = Colors.green;
                statusLabel = 'Disetujui';
              } else if (status == 'rejected') {
                statusColor = Colors.red;
                statusLabel = 'Ditolak';
              } else if (status == 'distributed') {
                statusColor = Colors.blue;
                statusLabel = 'Disalurkan';
              }

              return Card(
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(app['applicant_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                            child: Text(statusLabel, style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 11)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text('Program: ${app['program']?['title'] ?? '-'}', style: const TextStyle(fontSize: 12, color: AppTheme.primary, fontWeight: FontWeight.w600)),
                      Text('No. HP: ${app['phone']} • Kebutuhan: ${CurrencyFormatter.format(app['requested_amount'])}', style: const TextStyle(fontSize: 12)),
                      Text('Alasan: ${app['reason']}', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                      if (app['notes'] != null && app['notes'].toString().isNotEmpty) ...[
                        const SizedBox(height: 4),
                        Text('Catatan: ${app['notes']}', style: const TextStyle(fontSize: 11, color: Colors.brown, fontStyle: FontStyle.italic)),
                      ],
                      const SizedBox(height: 10),
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton.icon(
                          icon: const Icon(Icons.check_circle_outline, size: 16),
                          label: const Text('Verifikasi Pengajuan'),
                          onPressed: () => _showReviewBansosDialog(app),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }),
        ],
      ),
    );
  }

  // Tab 4: Kas & Marbot
  Widget _buildFinanceAndMarbotTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Pencatatan Kas Masjid', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ElevatedButton.icon(
                icon: const Icon(Icons.add, size: 16),
                label: const Text('Catat Kas'),
                onPressed: _showAddTransactionDialog,
              ),
            ],
          ),
          const SizedBox(height: 8),
          if (_transactions.isEmpty)
            const Card(child: Padding(padding: EdgeInsets.all(16), child: Center(child: Text('Belum ada transaksi kas.'))))
          else
            ..._transactions.take(5).map((tx) {
              final isIncome = tx['type'] == 'income';
              return Card(
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: isIncome ? Colors.green.shade50 : Colors.red.shade50,
                    child: Icon(isIncome ? Icons.arrow_downward : Icons.arrow_upward, color: isIncome ? Colors.green : Colors.red, size: 20),
                  ),
                  title: Text(tx['description'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                  subtitle: Text(DateFormatter.format(tx['transaction_date']), style: const TextStyle(fontSize: 11)),
                  trailing: Text(
                    '${isIncome ? '+' : '-'} ${CurrencyFormatter.format(tx['amount'])}',
                    style: TextStyle(fontWeight: FontWeight.bold, color: isIncome ? Colors.green : Colors.red, fontSize: 13),
                  ),
                ),
              );
            }),

          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Tugas Marbot Masjid', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ElevatedButton.icon(
                icon: const Icon(Icons.add_task, size: 16),
                label: const Text('Beri Tugas'),
                onPressed: _showAddTaskDialog,
              ),
            ],
          ),
          const SizedBox(height: 8),
          if (_tasks.isEmpty)
            const Card(child: Padding(padding: EdgeInsets.all(16), child: Center(child: Text('Belum ada tugas marbot.'))))
          else
            ..._tasks.take(5).map((task) => Card(
                  child: ListTile(
                    title: Text(task['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    subtitle: Text('Status: ${task['status']} • Prioritas: ${task['priority']}'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/marbot-tasks'),
                  ),
                )),
        ],
      ),
    );
  }
}
