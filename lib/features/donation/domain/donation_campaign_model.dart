class DonationCampaignModel {
  final int id;
  final String title;
  final String description;
  final num? targetAmount;
  final num collectedAmount;
  final String startDate;
  final String? endDate;
  final String status;
  final double progressPercentage;

  DonationCampaignModel({
    required this.id,
    required this.title,
    required this.description,
    this.targetAmount,
    required this.collectedAmount,
    required this.startDate,
    this.endDate,
    required this.status,
    required this.progressPercentage,
  });

  static num? _parseNum(dynamic val) {
    if (val == null) return null;
    if (val is num) return val;
    if (val is String) return num.tryParse(val);
    return null;
  }

  factory DonationCampaignModel.fromJson(Map<String, dynamic> json) {
    final collected = _parseNum(json['collected_amount']) ?? 0;
    final target = _parseNum(json['target_amount']);
    double progress = 0;
    if (target != null && target > 0) {
      progress = ((collected / target) * 100).clamp(0, 100).toDouble();
    }

    final rawProgress = _parseNum(json['progress_percentage']);

    return DonationCampaignModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] as String? ?? '',
      description: json['description'] as String? ?? '',
      targetAmount: target,
      collectedAmount: collected,
      startDate: json['start_date'] as String? ?? '',
      endDate: json['end_date'] as String?,
      status: json['status'] as String? ?? 'active',
      progressPercentage: rawProgress?.toDouble() ?? progress,
    );
  }
}
