<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    private string $storageDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storageDir = storage_path('app/feedback');
        if (File::exists($this->storageDir)) {
            File::deleteDirectory($this->storageDir);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->storageDir)) {
            File::deleteDirectory($this->storageDir);
        }
        parent::tearDown();
    }

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('LaravelApp');
        $response->assertSee('Форма заявки');
    }

    public function test_form_page_is_accessible(): void
    {
        $response = $this->get(route('form.show'));
        $response->assertStatus(200);
        $response->assertSee('Форма отправки заявки');
    }

    public function test_form_validation_fails_on_empty_request(): void
    {
        $response = $this->post(route('form.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertFalse(File::exists($this->storageDir));
    }

    public function test_form_submission_stores_json_file(): void
    {
        $payload = [
            'name' => 'Alexander',
            'email' => 'alexander@example.com',
            'subject' => 'Тестовая заявка',
            'message' => 'Проверка сохранения данных в файл JSON',
        ];

        $response = $this->post(route('form.store'), $payload);

        $response->assertRedirect(route('form.show'));
        $response->assertSessionHas('success');

        $this->assertTrue(File::exists($this->storageDir));
        $files = File::files($this->storageDir);
        $this->assertCount(1, $files);

        $savedContent = json_decode(File::get($files[0]), true);
        $this->assertEquals('Alexander', $savedContent['name']);
        $this->assertEquals('alexander@example.com', $savedContent['email']);
        $this->assertEquals('Тестовая заявка', $savedContent['subject']);
        $this->assertArrayHasKey('id', $savedContent);
        $this->assertArrayHasKey('created_at', $savedContent);
    }

    public function test_records_page_displays_stored_items(): void
    {
        $payload = [
            'name' => 'Ivan Ivanov',
            'email' => 'ivan@example.com',
            'subject' => 'Вопрос по курсу',
            'message' => 'Когда следующий семинар?',
        ];
        $this->post(route('form.store'), $payload);

        $response = $this->get(route('records.index'));
        $response->assertStatus(200);
        $response->assertSee('Ivan Ivanov');
        $response->assertSee('ivan@example.com');
        $response->assertSee('Вопрос по курсу');
    }
}
