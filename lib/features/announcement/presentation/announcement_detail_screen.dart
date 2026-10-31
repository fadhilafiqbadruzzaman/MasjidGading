import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/date_formatter.dart';
import '../domain/announcement_model.dart';

class AnnouncementDetailScreen extends StatelessWidget {
  final AnnouncementModel? announcement;

  const AnnouncementDetailScreen({super.key, this.announcement});

  @override
  Widget build(BuildContext context) {
    if (announcement == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Detail Pengumuman')),
        body: const Center(child: Text('Data tidak ditemukan')),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pengumuman'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                const Icon(Icons.calendar_today, size: 16, color: AppTheme.textMuted),
                const SizedBox(width: 6),
                Text(
                  DateFormatter.format(announcement!.publishedAt),
                  style: const TextStyle(color: AppTheme.textMuted, fontSize: 13),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Text(
              announcement!.title,
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: AppTheme.primary,
              ),
            ),
            const Divider(height: 32),
            Text(
              announcement!.content,
              style: const TextStyle(fontSize: 16, height: 1.6, color: AppTheme.textDark),
            ),
          ],
        ),
      ),
    );
  }
}
