num? _parseNum(dynamic val) {
  if (val == null) return null;
  if (val is num) return val;
  if (val is String) return num.tryParse(val);
  return null;
}

class DonationModel {
  final int id;
  final int campaignId;
  final num amount;
  final String paymentStatus;
  final String midtransOrderId;
  final String? paymentUrl;
  final String? campaignTitle;
  final String? createdAt;

  DonationModel({
    required this.id,
    required this.campaignId,
    required this.amount,
    required this.paymentStatus,
    required this.midtransOrderId,
    this.paymentUrl,
    this.campaignTitle,
    this.createdAt,
  });

  String get statusDisplay {
    switch (paymentStatus) {
      case 'success':
        return 'Berhasil';
      case 'pending':
        return 'Menunggu Pembayaran';
      case 'failed':
      case 'expire':
      case 'cancelled':
        return 'Gagal / Kedaluwarsa';
      default:
        return paymentStatus;
    }
  }

  factory DonationModel.fromJson(Map<String, dynamic> json) {
    return DonationModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      campaignId: json['campaign_id'] is int ? json['campaign_id'] as int : int.tryParse(json['campaign_id']?.toString() ?? '0') ?? 0,
      amount: _parseNum(json['amount']) ?? 0,
      paymentStatus: json['payment_status'] as String? ?? 'pending',
      midtransOrderId: json['midtrans_order_id'] as String? ?? '',
      paymentUrl: json['payment_url'] as String?,
      campaignTitle: json['campaign']?['title'] as String?,
      createdAt: json['created_at'] as String?,
    );
  }
}
