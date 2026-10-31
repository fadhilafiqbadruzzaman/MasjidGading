class AnnouncementModel {
  final int id;
  final String title;
  final String content;
  final String status;
  final String? publishedAt;

  AnnouncementModel({
    required this.id,
    required this.title,
    required this.content,
    required this.status,
    this.publishedAt,
  });

  factory AnnouncementModel.fromJson(Map<String, dynamic> json) {
    return AnnouncementModel(
      id: json['id'] is int ? json['id'] as int : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] as String? ?? '',
      content: json['content'] as String? ?? '',
      status: json['status'] as String? ?? 'published',
      publishedAt: json['published_at'] as String?,
    );
  }
}
