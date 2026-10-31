import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/date_formatter.dart';
import '../domain/transparency_model.dart';

class ProgramDetailScreen extends StatelessWidget {
  final ProgramAllocationModel? program;

  const ProgramDetailScreen({super.key, this.program});

  @override
  Widget build(BuildContext context) {
    if (program == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Detail Program')),
        body: const Center(child: Text('Data tidak ditemukan')),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Jejak Amanah Program'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              program!.title,
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppTheme.primary,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              program!.description,
              style: const TextStyle(color: AppTheme.textDark, height: 1.5),
            ),
            const SizedBox(height: 16),
            Card(
              margin: EdgeInsets.zero,
              color: AppTheme.primary.withOpacity(0.04),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    _buildItem('Total Alokasi Dana:', CurrencyFormatter.format(program!.totalAllocated), Colors.black87),
                    const SizedBox(height: 8),
                    _buildItem('Total Dana Terealisasi:', CurrencyFormatter.format(program!.totalRealized), Colors.green),
                    const SizedBox(height: 8),
                    _buildItem('Sisa Saldo Amanah:', CurrencyFormatter.format(program!.remainingBalance), Colors.blue),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              'Riwayat Bukti Penggunaan Dana (${program!.realizations.length})',
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            if (program!.realizations.isEmpty)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 20),
                child: Center(child: Text('Belum ada pembelanjaan realisasi yang dicatat.')),
              )
            else
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: program!.realizations.length,
                itemBuilder: (context, index) {
                  final item = program!.realizations[index];
                  return Card(
                    margin: const EdgeInsets.only(bottom: 8),
                    child: ListTile(
                      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                      leading: const CircleAvatar(
                        backgroundColor: AppTheme.primary,
                        child: Icon(Icons.receipt_long, color: Colors.white, size: 20),
                      ),
                      title: Text(item.description, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text('Tanggal: ${DateFormatter.format(item.realizationDate)}', style: const TextStyle(fontSize: 12)),
                          if (item.notes != null)
                            Text('Catatan: ${item.notes}', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                        ],
                      ),
                      trailing: Text(
                        CurrencyFormatter.format(item.amountUsed),
                        style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.green),
                      ),
                    ),
                  );
                },
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildItem(String title, String val, Color color) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: const TextStyle(fontSize: 13)),
        Text(val, style: TextStyle(fontWeight: FontWeight.bold, color: color, fontSize: 14)),
      ],
    );
  }
}
