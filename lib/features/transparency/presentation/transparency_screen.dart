import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../domain/transparency_model.dart';

class TransparencyScreen extends StatefulWidget {
  const TransparencyScreen({super.key});

  @override
  State<TransparencyScreen> createState() => _TransparencyScreenState();
}

class _TransparencyScreenState extends State<TransparencyScreen> {
  List<ProgramAllocationModel> _programs = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchPrograms();
  }

  Future<void> _fetchPrograms() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final res = await ApiClient().dio.get('/transparency');
      if (res.data['success'] == true) {
        final list = res.data['data'] as List? ?? [];
        setState(() {
          _programs = list.map((e) => ProgramAllocationModel.fromJson(e)).toList();
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = 'Gagal memuat data Jejak Amanah.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Jejak Amanah (Transparansi)'),
      ),
      body: RefreshIndicator(
        onRefresh: _fetchPrograms,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? Center(
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, size: 48, color: Colors.orange),
                          const SizedBox(height: 12),
                          Text(_error!, textAlign: TextAlign.center),
                          const SizedBox(height: 16),
                          ElevatedButton(onPressed: _fetchPrograms, child: const Text('Coba Lagi')),
                        ],
                      ),
                    ),
                  )
                : _programs.isEmpty
                    ? const Center(child: Text('Belum ada data alokasi program yang dipublikasikan.'))
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        itemCount: _programs.length,
                        itemBuilder: (context, index) {
                          final prog = _programs[index];
                          final percentage = prog.totalAllocated > 0
                              ? ((prog.totalRealized / prog.totalAllocated) * 100).clamp(0, 100)
                              : 0.0;

                          return Card(
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Row(
                                    children: [
                                      Icon(Icons.verified, color: AppTheme.primary, size: 18),
                                      SizedBox(width: 6),
                                      Text(
                                        'JEJAK AMANAH PROGRAM',
                                        style: TextStyle(
                                          color: AppTheme.primary,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 11,
                                          letterSpacing: 0.5,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    prog.title,
                                    style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    prog.description,
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(color: AppTheme.textMuted, fontSize: 13),
                                  ),
                                  const SizedBox(height: 14),
                                  LinearProgressIndicator(
                                    value: (percentage / 100).toDouble(),
                                    backgroundColor: Colors.grey.shade200,
                                    color: AppTheme.secondary,
                                    minHeight: 6,
                                  ),
                                  const SizedBox(height: 10),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Text('Alokasi Anggaran', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                          Text(
                                            CurrencyFormatter.format(prog.totalAllocated),
                                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                                          ),
                                        ],
                                      ),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.center,
                                        children: [
                                          const Text('Terealisasi', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                          Text(
                                            CurrencyFormatter.format(prog.totalRealized),
                                            style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.green, fontSize: 13),
                                          ),
                                        ],
                                      ),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          const Text('Sisa Saldo', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                          Text(
                                            CurrencyFormatter.format(prog.remainingBalance),
                                            style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.blue, fontSize: 13),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 12),
                                  SizedBox(
                                    width: double.infinity,
                                    child: OutlinedButton(
                                      onPressed: () => context.push('/transparency-detail', extra: prog),
                                      child: Text('Rincian Realisasi (${prog.realizations.length} Pembelanjaan)'),
                                    ),
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
}
