import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/date_formatter.dart';
import '../../../shared/providers/auth_provider.dart';
import '../domain/event_model.dart';

class EventDetailScreen extends ConsumerStatefulWidget {
  final EventModel? event;
  final int? eventId;

  const EventDetailScreen({super.key, this.event, this.eventId});

  @override
  ConsumerState<EventDetailScreen> createState() => _EventDetailScreenState();
}

class _EventDetailScreenState extends ConsumerState<EventDetailScreen> {
  bool _isRegistering = false;

  void _handleRegister(EventModel event) async {
    final authState = ref.read(authProvider);

    if (!authState.isAuthenticated) {
      final shouldLogin = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Login Diperlukan'),
          content: const Text('Anda harus masuk sebagai Jamaah terlebih dahulu untuk mendaftar kegiatan ini.'),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
            ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Masuk Sekarang')),
          ],
        ),
      );
      if (shouldLogin == true && mounted) {
        context.push('/login');
      }
      return;
    }

    setState(() => _isRegistering = true);

    try {
      final res = await ApiClient().dio.post('/events/${event.id}/register');
      if (mounted) {
        setState(() => _isRegistering = false);
        if (res.data['success'] == true) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Alhamdulillah! Anda berhasil mendaftar ke kegiatan ini.'),
              backgroundColor: Colors.green,
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(res.data['message']), backgroundColor: Colors.orange),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isRegistering = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Gagal mendaftar atau Anda sudah terdaftar.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _showParticipants(EventModel event) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => const Center(child: CircularProgressIndicator()),
    );

    try {
      final res = await ApiClient().dio.get('/admin/events/${event.id}/participants');
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
                  'Daftar Jamaah Terdaftar',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primary),
                ),
                Text(event.title, style: const TextStyle(fontSize: 14, color: AppTheme.textMuted)),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(8)),
                  child: Text(
                    'Total Pendaftar: ${participants.length} / ${event.capacity ?? '-'} Kuota',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.green.shade800),
                  ),
                ),
                const Divider(height: 20),
                Expanded(
                  child: participants.isEmpty
                      ? const Center(child: Text('Belum ada jamaah yang mendaftar pada kegiatan ini.'))
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
                                  Text('No. HP: ${user['phone'] ?? '-'}', style: const TextStyle(fontSize: 12, color: AppTheme.primary)),
                                  Text('Daftar: ${DateFormatter.formatWithTime(p['registered_at'])}', style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
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
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal memuat data pendaftar.'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final event = widget.event;

    if (event == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Detail Kegiatan')),
        body: const Center(child: Text('Data kegiatan tidak ditemukan')),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Kegiatan'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              event.title,
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: AppTheme.primary,
              ),
            ),
            const SizedBox(height: 16),
            Card(
              margin: EdgeInsets.zero,
              color: AppTheme.primary.withOpacity(0.04),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.calendar_month, color: AppTheme.primary, size: 20),
                        const SizedBox(width: 10),
                        Text(
                          DateFormatter.format(event.eventDate),
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        const Icon(Icons.schedule, color: AppTheme.primary, size: 20),
                        const SizedBox(width: 10),
                        Text('Pukul: ${event.eventTime} WIB'),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        const Icon(Icons.place, color: AppTheme.primary, size: 20),
                        const SizedBox(width: 10),
                        Expanded(child: Text(event.location)),
                      ],
                    ),
                    if (event.organizer != null) ...[
                      const SizedBox(height: 10),
                      Row(
                        children: [
                          const Icon(Icons.group, color: AppTheme.primary, size: 20),
                          const SizedBox(width: 10),
                          Text('Penyelenggara: ${event.organizer}'),
                        ],
                      ),
                    ],
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
            if (event.capacity != null) ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Kuota Peserta', style: TextStyle(fontWeight: FontWeight.bold)),
                  Text(
                    '${event.activeRegistrationsCount} / ${event.capacity} Terdaftar',
                    style: TextStyle(
                      color: event.isFull ? Colors.red : AppTheme.secondary,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              LinearProgressIndicator(
                value: event.capacity! > 0 ? (event.activeRegistrationsCount / event.capacity!).clamp(0.0, 1.0) : 0,
                color: event.isFull ? Colors.red : AppTheme.secondary,
                backgroundColor: Colors.grey.shade200,
              ),
              const SizedBox(height: 20),
            ],
            const Text(
              'Deskripsi Kegiatan',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              event.description,
              style: const TextStyle(fontSize: 15, height: 1.6, color: AppTheme.textDark),
            ),
            const SizedBox(height: 32),
            if (ref.watch(authProvider).user?.isAdmin == true) ...[
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppTheme.primary,
                    side: const BorderSide(color: AppTheme.primary, width: 1.5),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                  icon: const Icon(Icons.people_alt_outlined),
                  label: const Text('Lihat Daftar Jamaah Terdaftar (Admin)', style: TextStyle(fontWeight: FontWeight.bold)),
                  onPressed: () => _showParticipants(event),
                ),
              ),
              const SizedBox(height: 12),
            ],
            if (event.isRegistrationOpen && !event.isFull)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isRegistering ? null : () => _handleRegister(event),
                  child: _isRegistering
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                        )
                      : const Text('Daftar Sekarang (Gratis)', style: TextStyle(fontSize: 16)),
                ),
              )
            else if (event.isFull)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: const Text(
                  'Mohon maaf, kuota pendaftaran kegiatan ini sudah penuh.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
                ),
              )
            else
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Text(
                  'Pendaftaran belum dibuka atau sudah ditutup oleh pengurus.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppTheme.textMuted),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
