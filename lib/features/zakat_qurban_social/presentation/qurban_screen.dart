import 'package:flutter/material.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';

class QurbanScreen extends StatefulWidget {
  const QurbanScreen({super.key});

  @override
  State<QurbanScreen> createState() => _QurbanScreenState();
}

class _QurbanScreenState extends State<QurbanScreen> {
  List<dynamic> _programs = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchQurban();
  }

  Future<void> _fetchQurban() async {
    try {
      final res = await ApiClient().dio.get('/qurban');
      if (res.data['success'] == true) {
        setState(() {
          _programs = res.data['data'] as List? ?? [];
          _isLoading = false;
        });
      }
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Program Qurban Masjid BGC')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Card(
              margin: EdgeInsets.zero,
              color: AppTheme.secondary,
              child: Padding(
                padding: EdgeInsets.all(20),
                child: Column(
                  children: [
                    Icon(Icons.pets, size: 48, color: Colors.white),
                    SizedBox(height: 12),
                    Text(
                      'Tebar Hewan Qurban Berkah',
                      style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 6),
                    Text(
                      'Penerimaan pendaftaran shohibul qurban sapi (kolektif 7 orang) dan kambing/domba untuk Idul Adha.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.white70, fontSize: 13),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
            const Text('Program Qurban Tersedia', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            if (_isLoading)
              const Center(child: CircularProgressIndicator())
            else if (_programs.isEmpty)
              const Card(
                child: Padding(
                  padding: EdgeInsets.all(20),
                  child: Center(child: Text('Belum ada program qurban aktif pada periode ini.')),
                ),
              )
            else
              ..._programs.map((item) => Card(
                    child: ListTile(
                      leading: const CircleAvatar(
                        backgroundColor: AppTheme.primary,
                        child: Icon(Icons.check, color: Colors.white),
                      ),
                      title: Text(item['title'] ?? 'Program Qurban', style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text('Tahun ${item['year']} • Status: ${item['status']}'),
                    ),
                  )),
            const SizedBox(height: 20),
            const Text('Ketentuan Pendaftaran Qurban', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            const Text(
              '1. Shohibul qurban berhak mendapatkan 1/3 bagian daging qurban.\n2. Pembayaran dapat dicicil melalui bendahara kurban.\n3. Pemotongan dan pencacahan dilakukan secara higienis di area khusus Masjid BGC.',
              style: TextStyle(height: 1.6, color: AppTheme.textMuted),
            ),
          ],
        ),
      ),
    );
  }
}
