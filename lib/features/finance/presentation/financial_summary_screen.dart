import 'package:flutter/material.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/date_formatter.dart';
import '../domain/financial_summary_model.dart';

class FinancialSummaryScreen extends StatefulWidget {
  const FinancialSummaryScreen({super.key});

  @override
  State<FinancialSummaryScreen> createState() => _FinancialSummaryScreenState();
}

class _FinancialSummaryScreenState extends State<FinancialSummaryScreen> {
  List<FinancialSummaryModel> _summaries = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchSummaries();
  }

  Future<void> _fetchSummaries() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final res = await ApiClient().dio.get('/finance/summary');
      if (res.data['success'] == true) {
        final list = res.data['data'] as List? ?? [];
        setState(() {
          _summaries = list.map((e) => FinancialSummaryModel.fromJson(e)).toList();
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = 'Gagal memuat ringkasan keuangan. Silakan periksa koneksi.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Transparansi Keuangan'),
      ),
      body: RefreshIndicator(
        onRefresh: _fetchSummaries,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(_error!),
                        const SizedBox(height: 12),
                        ElevatedButton(onPressed: _fetchSummaries, child: const Text('Coba Lagi')),
                      ],
                    ),
                  )
                : _summaries.isEmpty
                    ? const Center(child: Text('Belum ada ringkasan keuangan yang dipublikasikan.'))
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        itemCount: _summaries.length,
                        itemBuilder: (context, index) {
                          final item = _summaries[index];
                          return Card(
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        'Periode: ${item.periodLabel}',
                                        style: const TextStyle(
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                          color: AppTheme.primary,
                                        ),
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: Colors.green.withOpacity(0.1),
                                          borderRadius: BorderRadius.circular(10),
                                        ),
                                        child: const Text(
                                          'Terverifikasi',
                                          style: TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    '${DateFormatter.format(item.periodStart)} s/d ${DateFormatter.format(item.periodEnd)}',
                                    style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                                  ),
                                  const Divider(height: 24),
                                  _buildRow('Saldo Awal', CurrencyFormatter.format(item.openingBalance), Colors.black87),
                                  const SizedBox(height: 6),
                                  _buildRow('Total Pemasukan (+)', CurrencyFormatter.format(item.totalIncome), Colors.green),
                                  const SizedBox(height: 6),
                                  _buildRow('Total Pengeluaran (-)', CurrencyFormatter.format(item.totalExpense), Colors.red),
                                  const Divider(height: 20),
                                  _buildRow('Saldo Akhir Kas', CurrencyFormatter.format(item.closingBalance), AppTheme.primary, isBold: true),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
      ),
    );
  }

  Widget _buildRow(String label, String value, Color color, {bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, fontSize: isBold ? 15 : 13)),
        Text(
          value,
          style: TextStyle(fontWeight: FontWeight.bold, color: color, fontSize: isBold ? 16 : 14),
        ),
      ],
    );
  }
}
