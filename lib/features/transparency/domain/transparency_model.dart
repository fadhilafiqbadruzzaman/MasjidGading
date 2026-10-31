num? _parseNum(dynamic val) {
  if (val == null) return null;
  if (val is num) return val;
  if (val is String) return num.tryParse(val);
  return null;
}

class ProgramRealizationModel {
  final int id;
  final String description;
  final num amountUsed;
  final String realizationDate;
  final String? notes;

  ProgramRealizationModel({
    required this.id,
    required this.description,
    required this.amountUsed,
    required this.realizationDate,
    this.notes,
  });

  factory ProgramRealizationModel.fromJson(Map<String, dynamic> json) {
    return ProgramRealizationModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      description: json['description'] as String? ?? '',
      amountUsed: _parseNum(json['amount_used']) ?? 0,
      realizationDate: json['realization_date'] as String? ?? '',
      notes: json['notes'] as String?,
    );
  }
}

class ProgramAllocationModel {
  final int id;
  final String title;
  final String description;
  final num totalAllocated;
  final num totalRealized;
  final num remainingBalance;
  final String? campaignTitle;
  final String status;
  final String? publishedAt;
  final List<ProgramRealizationModel> realizations;

  ProgramAllocationModel({
    required this.id,
    required this.title,
    required this.description,
    required this.totalAllocated,
    required this.totalRealized,
    required this.remainingBalance,
    this.campaignTitle,
    required this.status,
    this.publishedAt,
    required this.realizations,
  });

  factory ProgramAllocationModel.fromJson(Map<String, dynamic> json) {
    final list = json['realizations'] as List? ?? [];
    return ProgramAllocationModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] as String? ?? '',
      description: json['description'] as String? ?? '',
      totalAllocated: _parseNum(json['total_allocated']) ?? 0,
      totalRealized: _parseNum(json['total_realized']) ?? 0,
      remainingBalance: _parseNum(json['remaining_balance']) ?? 0,
      campaignTitle: json['campaign_title'] as String?,
      status: json['status'] as String? ?? 'active',
      publishedAt: json['published_at'] as String?,
      realizations: list.map((e) => ProgramRealizationModel.fromJson(e)).toList(),
    );
  }
}
