@extends('layouts.app')

@section('title', 'Список записей — LaravelApp')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2>Все сохраненные записи</h2>
            <p style="color: var(--text-muted);">
                Данные загружаются контроллером из файлов формата JSON (хранятся в storage/app/feedback/).
            </p>
        </div>
        <a href="{{ route('form.show') }}" class="btn">+ Новая запись</a>
    </div>

    @if (empty($records))
        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); border: 2px dashed var(--border); border-radius: 8px; margin-top: 1rem;">
            <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Пока нет ни одной сохраненной записи.</p>
            <p>Перейдите в раздел <a href="{{ route('form.show') }}" style="color: var(--primary);">«Форма заявки»</a> и отправьте данные.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Файл</th>
                        <th>Дата/Время</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Тема</th>
                        <th>Сообщение</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $item)
                        <tr>
                            <td><code style="font-size: 0.85rem; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $item['_file'] ?? '—' }}</code></td>
                            <td style="white-space: nowrap; font-size: 0.9rem; color: var(--text-muted);">{{ $item['created_at'] ?? '—' }}</td>
                            <td><strong>{{ $item['name'] ?? '—' }}</strong></td>
                            <td><a href="mailto:{{ $item['email'] ?? '' }}" style="color: var(--primary); text-decoration: none;">{{ $item['email'] ?? '—' }}</a></td>
                            <td>{{ $item['subject'] ?? '—' }}</td>
                            <td style="max-width: 300px; word-break: break-word;">{{ $item['message'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
