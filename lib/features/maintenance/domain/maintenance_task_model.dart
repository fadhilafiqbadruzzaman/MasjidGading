class MaintenanceTaskModel {
  final int id;
  final String title;
  final String description;
  final String? location;
  final String priority; // low, medium, high, urgent
  final String status; // pending, in_progress, completed, cancelled
  final String? dueDate;
  final String? startedAt;
  final String? completedAt;

  MaintenanceTaskModel({
    required this.id,
    required this.title,
    required this.description,
    this.location,
    required this.priority,
    required this.status,
    this.dueDate,
    this.startedAt,
    this.completedAt,
  });

  String get priorityDisplay {
    switch (priority) {
      case 'urgent':
        return 'Darurat';
      case 'high':
        return 'Tinggi';
      case 'medium':
        return 'Sedang';
      case 'low':
      default:
        return 'Rendah';
    }
  }

  String get statusDisplay {
    switch (status) {
      case 'pending':
        return 'Belum Dikerjakan';
      case 'in_progress':
        return 'Sedang Dikerjakan';
      case 'completed':
        return 'Selesai';
      case 'cancelled':
        return 'Dibatalkan';
      default:
        return status;
    }
  }

  factory MaintenanceTaskModel.fromJson(Map<String, dynamic> json) {
    return MaintenanceTaskModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] as String? ?? '',
      description: json['description'] as String? ?? '',
      location: json['location'] as String?,
      priority: json['priority'] as String? ?? 'medium',
      status: json['status'] as String? ?? 'pending',
      dueDate: json['due_date'] as String?,
      startedAt: json['started_at'] as String?,
      completedAt: json['completed_at'] as String?,
    );
  }
}
