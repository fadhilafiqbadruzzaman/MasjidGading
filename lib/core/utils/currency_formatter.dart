import 'package:intl/intl.dart';

class CurrencyFormatter {
  /// Accepts [num], [String] (e.g. "7500000.00" from Laravel decimal cast),
  /// or any [dynamic] value and formats it as Indonesian Rupiah.
  static String format(dynamic amount) {
    if (amount == null) return 'Rp 0';
    num value;
    if (amount is num) {
      value = amount;
    } else if (amount is String) {
      value = num.tryParse(amount) ?? 0;
    } else {
      value = 0;
    }
    final formatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );
    return formatter.format(value);
  }
}
