<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RankingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserRankChange()
    {
        $user = $this->fetchFirstUser();
        $oldRank = $user->ranking;

        // Update ranking to 3
        $this->setUserRank($user, 3);
        $user->refresh();
        $this->assertEquals(3, $user->ranking);

        // Update ranking to 1
        $this->setUserRank($user, 1);
        $user->refresh();
        $this->assertEquals(1, $user->ranking);

        // Revert back to old rank
        $user->ranking = $oldRank;
        $user->save();
        $user->refresh();
        $this->assertEquals($oldRank, $user->ranking);

    }

    public function testUserRankBuildsHistory()
    {
        $user = $this->fetchFirstUser();
        $historyCount = $user->rankings->count();
        $oldRank = $user->ranking;

        // Change rank three times
        for ($u=0; $u < 3; $u++) {
            $rank = rand(0, 5);

            $user->ranking = $rank;
            $user->save();
        }

        // Verify history increased by 3
        $user->refresh();
        $newCount = $user->rankings->count();
        $this->assertEquals($historyCount + 3, $newCount);

        // Revert back to old rank
        $user->ranking = $oldRank;
        $user->save();
        $user->refresh();
        $this->assertEquals($oldRank, $user->ranking);
    }

    /**
     * @param $user
     * @param int $rank
     */
    protected function setUserRank($user, int $rank): void
    {
        $response = $this->post(route('ranking.update', $user), [
            'value' => $rank,
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return User
     */
    protected function fetchFirstUser(): User
    {
        return User::where('user_type', 'player')->first();
    }
}
