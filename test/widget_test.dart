import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:masjidgading/core/theme/app_theme.dart';
import 'package:masjidgading/core/utils/currency_formatter.dart';
import 'package:masjidgading/features/announcement/domain/announcement_model.dart';
import 'package:masjidgading/features/announcement/presentation/announcement_detail_screen.dart';
import 'package:masjidgading/features/auth/domain/user_model.dart';
import 'package:masjidgading/features/event/domain/event_model.dart';
import 'package:masjidgading/features/finance/domain/financial_summary_model.dart';
import 'package:masjidgading/features/transparency/domain/transparency_model.dart';
import 'package:masjidgading/features/transparency/presentation/program_detail_screen.dart';

void main() {
  group('Unit Tests - Domain Models & Utilities', () {
    test('CurrencyFormatter formats IDR correctly', () {
      expect(CurrencyFormatter.format(50000), 'Rp 50.000');
      expect(CurrencyFormatter.format(12500000), 'Rp 12.500.000');
      expect(CurrencyFormatter.format('7500000.00'), 'Rp 7.500.000');
      expect(CurrencyFormatter.format('0.00'), 'Rp 0');
      expect(CurrencyFormatter.format(null), 'Rp 0');
    });

    test('UserModel handles roles and permissions correctly', () {
      final jamaah = UserModel(
        id: 1,
        name: 'Ahmad Jamaah',
        email: 'jamaah@bgc.com',
        role: 'jamaah',
      );
      expect(jamaah.isJamaah, true);
      expect(jamaah.isAdmin, false);
      expect(jamaah.isMarbot, false);
      expect(jamaah.roleDisplay, 'Jamaah');

      final marbot = UserModel(
        id: 2,
        name: 'Pak Joko Marbot',
        email: 'marbot@bgc.com',
        role: 'marbot',
      );
      expect(marbot.isMarbot, true);
      expect(marbot.isAdmin, false);
      expect(marbot.roleDisplay, 'Marbot Masjid');

      final admin = UserModel(
        id: 3,
        name: 'H. Dahlan Admin',
        email: 'admin@bgc.com',
        role: 'admin_masjid',
      );
      expect(admin.isAdmin, true);
      expect(admin.isMarbot, false);
      expect(admin.roleDisplay, 'Admin Masjid');
    });

    test('EventModel calculates capacity and full status accurately', () {
      final eventWithSlots = EventModel(
        id: 1,
        title: 'Kajian Subuh',
        description: 'Tafsir Surat Al-Kahfi',
        location: 'Ruang Sholat Utama',
        eventDate: '2026-10-30',
        eventTime: '05:00:00',
        capacity: 100,
        activeRegistrationsCount: 45,
        isRegistrationOpen: true,
        status: 'planned',
      );
      expect(eventWithSlots.isFull, false);
      expect(eventWithSlots.remainingSlots, 55);

      final fullEvent = EventModel(
        id: 2,
        title: 'Kajian Akbar',
        description: 'Kajian Spesial Akhir Tahun',
        location: 'Aula Serbaguna',
        eventDate: '2026-11-01',
        eventTime: '09:00:00',
        capacity: 50,
        activeRegistrationsCount: 50,
        isRegistrationOpen: true,
        status: 'planned',
      );
      expect(fullEvent.isFull, true);
      expect(fullEvent.remainingSlots, 0);
    });

    test('FinancialSummaryModel handles balance data properly', () {
      final summary = FinancialSummaryModel.fromJson({
        'period_label': 'Oktober 2026',
        'period_start': '2026-10-01',
        'period_end': '2026-10-31',
        'opening_balance': 25000000,
        'total_income': 10000000,
        'total_expense': 4000000,
        'closing_balance': 31000000,
      });

      expect(summary.periodLabel, 'Oktober 2026');
      expect(summary.openingBalance, 25000000);
      expect(summary.totalIncome, 10000000);
      expect(summary.totalExpense, 4000000);
      // BR-05: Saldo Akhir = Saldo Awal + Pemasukan - Pengeluaran
      expect(summary.closingBalance, summary.openingBalance + summary.totalIncome - summary.totalExpense);
    });
  });

  group('Widget Presentation Tests', () {
    testWidgets('AnnouncementDetailScreen renders title and content correctly', (WidgetTester tester) async {
      final announcement = AnnouncementModel(
        id: 1,
        title: 'Kajian Akbar Spesial',
        content: 'Mari hadiri kajian akbar bersama Ustadz Salim.',
        status: 'published',
        publishedAt: '2026-10-24T10:00:00Z',
      );

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: AnnouncementDetailScreen(announcement: announcement),
        ),
      );

      expect(find.text('Kajian Akbar Spesial'), findsOneWidget);
      expect(find.text('Mari hadiri kajian akbar bersama Ustadz Salim.'), findsOneWidget);
      expect(find.text('Detail Pengumuman'), findsOneWidget);
    });

    testWidgets('ProgramDetailScreen renders Jejak Amanah allocations and realizations', (WidgetTester tester) async {
      final program = ProgramAllocationModel(
        id: 1,
        title: 'Renovasi Kamar Mandi Difabel',
        description: 'Pengadaan kloset duduk dan pegangan stainless.',
        totalAllocated: 5000000,
        totalRealized: 2000000,
        remainingBalance: 3000000,
        status: 'active',
        realizations: [
          ProgramRealizationModel(
            id: 101,
            description: 'Pembelian 2 unit kloset duduk TOTO',
            amountUsed: 2000000,
            realizationDate: '2026-10-20',
            notes: 'Faktur toko TB Berkah',
          ),
        ],
      );

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: ProgramDetailScreen(program: program),
        ),
      );

      expect(find.text('Renovasi Kamar Mandi Difabel'), findsOneWidget);
      expect(find.text('Pembelian 2 unit kloset duduk TOTO'), findsOneWidget);
      expect(find.text('Rp 2.000.000'), findsNWidgets(2)); // Total terealisasi & item realization
    });
  });
}
