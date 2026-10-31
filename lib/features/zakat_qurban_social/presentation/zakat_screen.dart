import 'package:flutter/material.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';

class ZakatScreen extends StatefulWidget {
  const ZakatScreen({super.key});

  @override
  State<ZakatScreen> createState() => _ZakatScreenState();
}

class _ZakatScreenState extends State<ZakatScreen> {
  Map<String, dynamic>? _summary;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchZakatSummary();
  }

  Future<void> _fetchZakatSummary() async {
    try {
      final res = await ApiClient().dio.get('/zakat/summary');
      if (res.data['success'] == true) {
        setState(() {
          _summary = res.data['data'];
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
      appBar: AppBar(title: const Text('Layanan Zakat Masjid BGC')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Card(
              margin: EdgeInsets.zero,
              color: AppTheme.primary,
              child: Padding(
                padding: EdgeInsets.all(20),
                child: Column(
                  children: [
                    Icon(Icons.volunteer_activism, size: 48, color: Colors.white),
                    SizedBox(height: 12),
                    Text(
                      'Penerimaan & Penyaluran Zakat',
                      style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 6),
                    Text(
                      'Masjid Bukit Gading Cisoka melayani penghimpunan dan penyaluran Zakat Fitrah, Zakat Maal, dan Zakat Profesi secara amanah.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.white70, fontSize: 13),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
            const Text('Ringkasan Penerimaan Tahun Ini', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            if (_isLoading)
              const Center(child: CircularProgressIndicator())
            else ...[
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      _row('Zakat Fitrah', CurrencyFormatter.format(_summary?['total_fitrah'] ?? 0)),
                      const Divider(height: 16),
                      _row('Zakat Maal', CurrencyFormatter.format(_summary?['total_maal'] ?? 0)),
                      const Divider(height: 16),
                      _row('Zakat Profesi', CurrencyFormatter.format(_summary?['total_profesi'] ?? 0)),
                      const Divider(height: 16),
                      _row('Total Terhimpun', CurrencyFormatter.format(_summary?['total_collected'] ?? 0), isBold: true, color: AppTheme.primary),
                      const Divider(height: 16),
                      _row('Telah Disalurkan ke Asnaf', CurrencyFormatter.format(_summary?['total_distributed'] ?? 0), isBold: true, color: Colors.green),
                    ],
                  ),
                ),
              ),
            ],
            const SizedBox(height: 20),
            const Text('Konsultasi & Penyerahan Zakat', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            const Text(
              'Untuk pembayaran zakat langsung atau konfirmasi transfer zakat maal, silakan hubungi amil zakat Masjid BGC di sekretariat masjid setiap hari kerja.',
              style: TextStyle(color: AppTheme.textMuted, height: 1.5),
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(String label, String val, {bool isBold = false, Color? color}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal)),
        Text(val, style: TextStyle(fontWeight: FontWeight.bold, color: color ?? Colors.black87)),
      ],
    );
  }
}
