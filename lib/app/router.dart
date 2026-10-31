import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../features/admin/presentation/admin_hub_screen.dart';
import '../features/announcement/domain/announcement_model.dart';
import '../features/announcement/presentation/announcement_detail_screen.dart';
import '../features/announcement/presentation/announcement_list_screen.dart';
import '../features/auth/presentation/login_screen.dart';
import '../features/auth/presentation/register_screen.dart';
import '../features/donation/domain/donation_campaign_model.dart';
import '../features/donation/presentation/donate_screen.dart';
import '../features/donation/presentation/donation_campaign_list_screen.dart';
import '../features/donation/presentation/my_donations_screen.dart';
import '../features/event/domain/event_model.dart';
import '../features/event/presentation/event_detail_screen.dart';
import '../features/event/presentation/event_list_screen.dart';
import '../features/finance/presentation/financial_summary_screen.dart';
import '../features/maintenance/presentation/facility_report_screen.dart';
import '../features/maintenance/presentation/marbot_tasks_screen.dart';
import '../features/profile/presentation/profile_screen.dart';
import '../features/transparency/domain/transparency_model.dart';
import '../features/transparency/presentation/program_detail_screen.dart';
import '../features/transparency/presentation/transparency_screen.dart';
import '../features/zakat_qurban_social/presentation/qurban_screen.dart';
import '../features/zakat_qurban_social/presentation/social_program_screen.dart';
import '../features/zakat_qurban_social/presentation/zakat_screen.dart';
import '../shared/widgets/app_scaffold.dart';
import 'home_screen.dart';

final GlobalKey<NavigatorState> _rootNavigatorKey = GlobalKey<NavigatorState>();
final GlobalKey<NavigatorState> _shellNavigatorKey = GlobalKey<NavigatorState>();

final appRouter = GoRouter(
  navigatorKey: _rootNavigatorKey,
  initialLocation: '/',
  routes: [
    // ShellRoute for Bottom Navigation
    ShellRoute(
      navigatorKey: _shellNavigatorKey,
      builder: (context, state, child) => AppScaffold(child: child),
      routes: [
        GoRoute(
          path: '/',
          pageBuilder: (context, state) => const NoTransitionPage(child: HomeScreen()),
        ),
        GoRoute(
          path: '/announcements',
          pageBuilder: (context, state) => const NoTransitionPage(child: AnnouncementListScreen()),
        ),
        GoRoute(
          path: '/events',
          pageBuilder: (context, state) => const NoTransitionPage(child: EventListScreen()),
        ),
        GoRoute(
          path: '/donations',
          pageBuilder: (context, state) => const NoTransitionPage(child: DonationCampaignListScreen()),
        ),
        GoRoute(
          path: '/profile',
          pageBuilder: (context, state) => const NoTransitionPage(child: ProfileScreen()),
        ),
      ],
    ),

    // Sub-routes / Detail pages outside shell
    GoRoute(
      path: '/login',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/register',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const RegisterScreen(),
    ),
    GoRoute(
      path: '/announcements/:id',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) {
        final extra = state.extra as AnnouncementModel?;
        return AnnouncementDetailScreen(announcement: extra);
      },
    ),
    GoRoute(
      path: '/events/:id',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) {
        final extra = state.extra as EventModel?;
        return EventDetailScreen(event: extra);
      },
    ),
    GoRoute(
      path: '/donate',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) {
        final extra = state.extra as DonationCampaignModel?;
        return DonateScreen(campaign: extra);
      },
    ),
    GoRoute(
      path: '/my-donations',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const MyDonationsScreen(),
    ),
    GoRoute(
      path: '/finance',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const FinancialSummaryScreen(),
    ),
    GoRoute(
      path: '/transparency',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const TransparencyScreen(),
    ),
    GoRoute(
      path: '/transparency-detail',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) {
        final extra = state.extra as ProgramAllocationModel?;
        return ProgramDetailScreen(program: extra);
      },
    ),
    GoRoute(
      path: '/marbot-tasks',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const MarbotTasksScreen(),
    ),
    GoRoute(
      path: '/facility-report',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const FacilityReportScreen(),
    ),
    GoRoute(
      path: '/admin-hub',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const AdminHubScreen(),
    ),
    GoRoute(
      path: '/zakat',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const ZakatScreen(),
    ),
    GoRoute(
      path: '/qurban',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const QurbanScreen(),
    ),
    GoRoute(
      path: '/social',
      parentNavigatorKey: _rootNavigatorKey,
      builder: (context, state) => const SocialProgramScreen(),
    ),
  ],
);
