import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/date_formatter.dart';
import '../domain/maintenance_task_model.dart';

class MarbotTasksScreen extends StatefulWidget {
  const MarbotTasksScreen({super.key});

  @override
  State<MarbotTasksScreen> createState() => _MarbotTasksScreenState();
}

class _MarbotTasksScreenState extends State<MarbotTasksScreen> {
  List<MaintenanceTaskModel> _tasks = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchTasks();
  }

  Future<void> _fetchTasks() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final res = await ApiClient().dio.get('/marbot/tasks');
      if (res.data['success'] == true) {
        final list = res.data['data'] as List? ?? [];
        setState(() {
          _tasks = list.map((e) => MaintenanceTaskModel.fromJson(e)).toList();
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = 'Gagal memuat tugas Marbot. Pastikan Anda masuk sebagai Marbot.';
      });
    }
  }

  void _showUpdateDialog(MaintenanceTaskModel task) {
    String selectedStatus = task.status;
    final notesController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) => AlertDialog(
          title: Text('Perbarui Tugas #${task.id}'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(task.title, style: const TextStyle(fontWeight: FontWeight.bold)),
              const SizedBox(height: 14),
              const Text('Status Pengerjaan:'),
              DropdownButton<String>(
                value: selectedStatus,
                isExpanded: true,
                items: const [
                  DropdownMenuItem(value: 'pending', child: Text('Belum Dikerjakan')),
                  DropdownMenuItem(value: 'in_progress', child: Text('Sedang Dikerjakan')),
                  DropdownMenuItem(value: 'completed', child: Text('Selesai')),
                ],
                onChanged: (val) {
                  if (val != null) {
                    setDialogState(() => selectedStatus = val);
                  }
                },
              ),
              const SizedBox(height: 12),
              TextField(
                controller: notesController,
                maxLines: 2,
                decoration: const InputDecoration(
                  labelText: 'Catatan Pengerjaan Marbot',
                  hintText: 'Misal: Selesai dibersihkan & filter dicuci.',
                ),
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () async {
                Navigator.pop(ctx);
                try {
                  await ApiClient().dio.put('/marbot/tasks/${task.id}/status', data: {
                    'status': selectedStatus,
                    'notes': notesController.text.trim(),
                  });
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Status tugas berhasil diperbarui'), backgroundColor: Colors.green),
                    );
                    _fetchTasks();
                  }
                } catch (e) {
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Gagal memperbarui status tugas'), backgroundColor: Colors.red),
                    );
                  }
                }
              },
              child: const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tugas Marbot Masjid'),
        actions: [
          IconButton(
            icon: const Icon(Icons.report_problem_outlined),
            tooltip: 'Lapor Kerusakan',
            onPressed: () => context.push('/facility-report'),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchTasks,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? Center(
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(_error!, textAlign: TextAlign.center),
                          const SizedBox(height: 12),
                          ElevatedButton(onPressed: _fetchTasks, child: const Text('Coba Lagi')),
                        ],
                      ),
                    ),
                  )
                : _tasks.isEmpty
                    ? const Center(child: Text('Alhamdulillah, tidak ada tugas pemeliharaan saat ini.'))
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        itemCount: _tasks.length,
                        itemBuilder: (context, index) {
                          final task = _tasks[index];
                          final isCompleted = task.status == 'completed';

                          return Card(
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: task.priority == 'urgent' || task.priority == 'high'
                                              ? Colors.red.shade50
                                              : Colors.blue.shade50,
                                          borderRadius: BorderRadius.circular(10),
                                        ),
                                        child: Text(
                                          'Prioritas: ${task.priorityDisplay}',
                                          style: TextStyle(
                                            fontSize: 11,
                                            fontWeight: FontWeight.bold,
                                            color: task.priority == 'urgent' || task.priority == 'high'
                                                ? Colors.red.shade700
                                                : Colors.blue.shade700,
                                          ),
                                        ),
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: isCompleted ? Colors.green.shade50 : Colors.orange.shade50,
                                          borderRadius: BorderRadius.circular(12),
                                        ),
                                        child: Text(
                                          task.statusDisplay,
                                          style: TextStyle(
                                            fontSize: 11,
                                            fontWeight: FontWeight.bold,
                                            color: isCompleted ? Colors.green.shade800 : Colors.orange.shade800,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 10),
                                  Text(
                                    task.title,
                                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 6),
                                  Text(
                                    task.description,
                                    style: const TextStyle(fontSize: 13, color: AppTheme.textDark),
                                  ),
                                  if (task.location != null) ...[
                                    const SizedBox(height: 6),
                                    Row(
                                      children: [
                                        const Icon(Icons.place_outlined, size: 14, color: AppTheme.textMuted),
                                        const SizedBox(width: 4),
                                        Text('Lokasi: ${task.location}', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                                      ],
                                    ),
                                  ],
                                  if (task.dueDate != null) ...[
                                    const SizedBox(height: 4),
                                    Row(
                                      children: [
                                        const Icon(Icons.event_outlined, size: 14, color: AppTheme.textMuted),
                                        const SizedBox(width: 4),
                                        Text('Tenggat: ${DateFormatter.format(task.dueDate)}', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                                      ],
                                    ),
                                  ],
                                  const SizedBox(height: 12),
                                  SizedBox(
                                    width: double.infinity,
                                    child: ElevatedButton.icon(
                                      icon: const Icon(Icons.edit_note, size: 18),
                                      label: const Text('Perbarui Status Pekerjaan'),
                                      onPressed: () => _showUpdateDialog(task),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/facility-report'),
        icon: const Icon(Icons.add_alert),
        label: const Text('Lapor Kerusakan'),
        backgroundColor: Colors.orange.shade800,
        foregroundColor: Colors.white,
      ),
    );
  }
}
