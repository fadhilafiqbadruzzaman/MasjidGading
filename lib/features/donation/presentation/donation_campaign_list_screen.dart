import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/currency_formatter.dart';
import '../domain/donation_campaign_model.dart';

class DonationCampaignListScreen extends StatefulWidget {
  const DonationCampaignListScreen({super.key});

  @override
  State<DonationCampaignListScreen> createState() => _DonationCampaignListScreenState();
}

class _DonationCampaignListScreenState extends State<DonationCampaignListScreen> {
  List<DonationCampaignModel> _campaigns = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchCampaigns();
  }

  Future<void> _fetchCampaigns() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final res = await ApiClient().dio.get('/donation-campaigns');
      if (res.data['success'] == true) {
        final list = res.data['data']['data'] as List? ?? [];
        setState(() {
          _campaigns = list.map((e) => DonationCampaignModel.fromJson(e)).toList();
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = 'Gagal memuat program donasi.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Program Donasi & Infaq'),
        actions: [
          IconButton(
            icon: const Icon(Icons.history),
            tooltip: 'Riwayat Donasi Saya',
            onPressed: () => context.push('/my-donations'),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchCampaigns,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(_error!),
                        const SizedBox(height: 12),
                        ElevatedButton(onPressed: _fetchCampaigns, child: const Text('Coba Lagi')),
                      ],
                    ),
                  )
                : _campaigns.isEmpty
                    ? const Center(child: Text('Belum ada kampanye donasi aktif.'))
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        itemCount: _campaigns.length,
                        itemBuilder: (context, index) {
                          final campaign = _campaigns[index];
                          return Card(
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    campaign.title,
                                    style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: AppTheme.textDark,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    campaign.description,
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(color: AppTheme.textMuted, fontSize: 13),
                                  ),
                                  const SizedBox(height: 16),
                                  LinearProgressIndicator(
                                    value: (campaign.progressPercentage / 100).clamp(0.0, 1.0),
                                    backgroundColor: Colors.grey.shade200,
                                    color: AppTheme.secondary,
                                    minHeight: 8,
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  const SizedBox(height: 12),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Text('Terkumpul', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                          Text(
                                            CurrencyFormatter.format(campaign.collectedAmount),
                                            style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primary, fontSize: 14),
                                          ),
                                        ],
                                      ),
                                      if (campaign.targetAmount != null)
                                        Column(
                                          crossAxisAlignment: CrossAxisAlignment.end,
                                          children: [
                                            const Text('Target', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                                            Text(
                                              CurrencyFormatter.format(campaign.targetAmount),
                                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                            ),
                                          ],
                                        ),
                                    ],
                                  ),
                                  const SizedBox(height: 14),
                                  SizedBox(
                                    width: double.infinity,
                                    child: ElevatedButton(
                                      onPressed: () => context.push('/donate', extra: campaign),
                                      child: const Text('Donasi Sekarang'),
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
