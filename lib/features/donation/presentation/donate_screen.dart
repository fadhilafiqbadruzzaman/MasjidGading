import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../shared/providers/auth_provider.dart';
import '../domain/donation_campaign_model.dart';

class DonateScreen extends ConsumerStatefulWidget {
  final DonationCampaignModel? campaign;

  const DonateScreen({super.key, this.campaign});

  @override
  ConsumerState<DonateScreen> createState() => _DonateScreenState();
}

class _DonateScreenState extends ConsumerState<DonateScreen> {
  final _amountController = TextEditingController(text: '50000');
  final _notesController = TextEditingController();
  bool _isLoading = false;

  final List<int> _presetAmounts = [25000, 50000, 100000, 250000, 500000, 1000000];

  @override
  void dispose() {
    _amountController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _submitDonation() async {
    final authState = ref.read(authProvider);

    // Rule: Donasi WAJIB LOGIN
    if (!authState.isAuthenticated) {
      final shouldLogin = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Login Diperlukan'),
          content: const Text('Sesuai ketentuan Masjid BGC, donasi online mewajibkan donatur masuk ke akun jamaah.'),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
            ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Masuk')),
          ],
        ),
      );
      if (shouldLogin == true && mounted) {
        context.push('/login');
      }
      return;
    }

    final amount = double.tryParse(_amountController.text.replaceAll(RegExp(r'[^0-9]'), ''));
    if (amount == null || amount < 10000) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Nominal donasi minimal Rp 10.000'), backgroundColor: Colors.red),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final res = await ApiClient().dio.post('/donations/initiate', data: {
        'campaign_id': widget.campaign?.id ?? 1,
        'amount': amount,
        'notes': _notesController.text.trim(),
      });

      if (mounted) {
        setState(() => _isLoading = false);
        if (res.data['success'] == true) {
          final data = res.data['data'];
          final orderId = data['order_id'];

          await showDialog(
            context: context,
            barrierDismissible: false,
            builder: (ctx) => AlertDialog(
              title: const Text('Inisiasi Pembayaran Midtrans'),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Permintaan donasi Anda telah tercatat dengan aman.'),
                  const SizedBox(height: 12),
                  Text('No. Order: $orderId', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                  const SizedBox(height: 6),
                  Text('Nominal: ${CurrencyFormatter.format(amount)}', style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.bold, fontSize: 15)),
                  const SizedBox(height: 12),
                  const Text(
                    'Pembayaran diproses melalui payment gateway Midtrans (GoPay, QRIS, Transfer Bank, ShopeePay). Transaksi akan otomatis terverifikasi ke kas masjid setelah pembayaran diterima.',
                    style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
                  ),
                ],
              ),
              actions: [
                ElevatedButton(
                  onPressed: () {
                    Navigator.pop(ctx);
                    context.go('/my-donations');
                  },
                  child: const Text('Lihat Riwayat Donasi'),
                ),
              ],
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
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal membuat permintaan donasi.'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final campaign = widget.campaign;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Formulir Donasi Online'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (campaign != null) ...[
              Card(
                margin: EdgeInsets.zero,
                color: AppTheme.primary.withOpacity(0.05),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Tujuan Donasi:', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                      const SizedBox(height: 4),
                      Text(
                        campaign.title,
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.primary),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),
            ],

            const Text('Pilih Nominal Donasi', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            const SizedBox(height: 12),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _presetAmounts.map((preset) {
                final isSelected = _amountController.text == preset.toString();
                return ChoiceChip(
                  label: Text(CurrencyFormatter.format(preset)),
                  selected: isSelected,
                  selectedColor: AppTheme.primary,
                  labelStyle: TextStyle(
                    color: isSelected ? Colors.white : AppTheme.textDark,
                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                  ),
                  onSelected: (selected) {
                    if (selected) {
                      setState(() {
                        _amountController.text = preset.toString();
                      });
                    }
                  },
                );
              }).toList(),
            ),
            const SizedBox(height: 16),

            TextFormField(
              controller: _amountController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: 'Nominal Lainnya (Rp)',
                prefixText: 'Rp ',
              ),
            ),
            const SizedBox(height: 16),

            TextFormField(
              controller: _notesController,
              maxLines: 2,
              decoration: const InputDecoration(
                labelText: 'Doa / Catatan Keberkahan (Opsional)',
                hintText: 'Misal: Untuk almarhum orang tua tercinta...',
              ),
            ),
            const SizedBox(height: 12),

            const Row(
              children: [
                Icon(Icons.shield_outlined, size: 16, color: Colors.green),
                SizedBox(width: 6),
                Text('Pembayaran Terenkripsi Aman via Midtrans', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
              ],
            ),
            const SizedBox(height: 32),

            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _submitDonation,
                child: _isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Text('Lanjutkan Pembayaran Donasi', style: TextStyle(fontSize: 16)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
