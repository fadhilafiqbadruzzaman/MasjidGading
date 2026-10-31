class EventModel {
  final int id;
  final String title;
  final String description;
  final String location;
  final String eventDate;
  final String eventTime;
  final String? organizer;
  final int? capacity;
  final int activeRegistrationsCount;
  final bool isRegistrationOpen;
  final String status;

  EventModel({
    required this.id,
    required this.title,
    required this.description,
    required this.location,
    required this.eventDate,
    required this.eventTime,
    this.organizer,
    this.capacity,
    this.activeRegistrationsCount = 0,
    required this.isRegistrationOpen,
    required this.status,
  });

  bool get isFull {
    if (capacity == null || capacity! <= 0) return false;
    return activeRegistrationsCount >= capacity!;
  }

  int get remainingSlots {
    if (capacity == null || capacity! <= 0) return 999;
    return (capacity! - activeRegistrationsCount).clamp(0, capacity!);
  }

  factory EventModel.fromJson(Map<String, dynamic> json) {
    int? parseSafeInt(dynamic v) {
      if (v == null) return null;
      if (v is int) return v;
      if (v is num) return v.toInt();
      return int.tryParse(v.toString());
    }

    return EventModel(
      id: parseSafeInt(json['id']) ?? 0,
      title: json['title'] as String? ?? '',
      description: json['description'] as String? ?? '',
      location: json['location'] as String? ?? '',
      eventDate: json['event_date'] as String? ?? '',
      eventTime: json['event_time'] as String? ?? '',
      organizer: json['organizer'] as String?,
      capacity: parseSafeInt(json['capacity']),
      activeRegistrationsCount: parseSafeInt(json['active_registrations_count']) ?? 0,
      isRegistrationOpen: json['is_registration_open'] == 1 || json['is_registration_open'] == true,
      status: json['status'] as String? ?? 'planned',
    );
  }
}
