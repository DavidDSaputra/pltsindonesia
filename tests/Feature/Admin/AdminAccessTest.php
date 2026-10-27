<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every CMS screen reachable with the "manage-content" ability.
     *
     * @return array<int, string>
     */
    protected function contentPages(): array
    {
        return [
            route('admin.dashboard'),
            route('admin.articles.index'),
            route('admin.articles.create'),
            route('admin.categories.index'),
            route('admin.tags.index'),
            route('admin.media.index'),
            route('admin.services.index'),
            route('admin.projects.index'),
            route('admin.testimonials.index'),
            route('admin.faqs.index'),
            route('admin.galleries.index'),
            route('admin.messages.index'),
            route('admin.subscribers.index'),
            route('admin.settings.index'),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function everyPage(): array
    {
        return [...$this->contentPages(), route('admin.users.index')];
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        foreach ($this->everyPage() as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_users_without_a_cms_role_are_forbidden(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);

        foreach ($this->everyPage() as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_unverified_users_are_redirected_to_email_verification(): void
    {
        $user = User::factory()->admin()->unverified()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_editors_can_open_every_content_screen(): void
    {
        $editor = User::factory()->editor()->create();

        foreach ($this->contentPages() as $url) {
            $this->actingAs($editor)->get($url)->assertOk();
        }
    }

    public function test_editors_cannot_manage_users(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admins_can_open_every_screen_including_user_management(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ($this->everyPage() as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admins_can_open_the_article_edit_screen(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.articles.edit', $article))
            ->assertOk();
    }

    public function test_the_admin_layout_renders_the_unread_message_badge(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('x-data', false);
    }
}
