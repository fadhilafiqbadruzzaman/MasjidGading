num? _parseNum(dynamic val) {
  if (val == null) return null;
  if (val is num) return val;
  if (val is String) return num.tryParse(val);
  return null;
}

class FinancialSummaryModel {
  final String periodLabel;
  final String periodStart;
  final String periodEnd;
  final num openingBalance;
  final num totalIncome;
  final num totalExpense;
  final num closingBalance;

  FinancialSummaryModel({
    required this.periodLabel,
    required this.periodStart,
    required this.periodEnd,
    required this.openingBalance,
    required this.totalIncome,
    required this.totalExpense,
    required this.closingBalance,
  });

  factory FinancialSummaryModel.fromJson(Map<String, dynamic> json) {
    return FinancialSummaryModel(
      periodLabel: json['period_label'] as String? ?? '',
      periodStart: json['period_start'] as String? ?? '',
      periodEnd: json['period_end'] as String? ?? '',
      openingBalance: _parseNum(json['opening_balance']) ?? 0,
      totalIncome: _parseNum(json['total_income']) ?? 0,
      totalExpense: _parseNum(json['total_expense']) ?? 0,
      closingBalance: _parseNum(json['closing_balance']) ?? 0,
    );
  }
}
