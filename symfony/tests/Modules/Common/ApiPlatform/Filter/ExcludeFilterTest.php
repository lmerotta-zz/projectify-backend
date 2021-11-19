<?php

namespace App\Tests\Modules\Common\ApiPlatform\Filter;

use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use App\Tests\Helpers\APITestCase;

class ExcludeFilterTest extends APITestCase
{
    public function testItExcludesTheUsersInTheSpecifiedTeams(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $this->createUser('second@test.com', 'second', 'user');
        $this->createUser('third@test.com', 'third', 'user');

        // first user creates a team
        $createTeamMutation = <<<'GQL'
            mutation create($name: String!) {
                createTeam(input: { name: $name }) {
                    team {
                      id
                      name
                    }
                }
            }
        GQL;

        $this->login('third@test.com');
        $response = $this->graphql($createTeamMutation, ['name' => 'First team1']);
        $this->logout();

        $teamIri = json_decode($response->getContent(), true)['data']['createTeam']['team']['id'];
        $teamIdParts = explode('/', $teamIri);
        $teamId = end($teamIdParts);

        // get the two first users, and the team. Add each user to the team
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $team = $em->getRepository(Team::class)->find($teamId);

        $first = $em->getRepository(User::class)->findOneByEmail('first@test.com');
        $second = $em->getRepository(User::class)->findOneByEmail('second@test.com');

        $first->addTeam($team);
        $second->addTeam($team);

        $em->flush();

        // assert the first user cannot view anything, except the third user
        $listUsersQuery = <<<'GQL'
            query listUsers($email: String, $excludeTeam: String!) {
                users(email: $email, exclude_teams: $excludeTeam) {
                    edges {
                        node {
                            email
                        }
                    }
                }
            }
        GQL;

        $this->login('first@test.com');
        $this->graphql($listUsersQuery, ['excludeTeam' => $teamIri]);
        $this->assertJsonEquals(["data" => ["users" => ["edges" => []]]]);
        $this->graphql($listUsersQuery, ['email' => 'third@test.com', 'excludeTeam' => $teamIri]);
        $this->assertJsonEquals(["data" => ["users" => ["edges" => [['node' => ['email' => 'third@test.com']]]]]]);
    }
}
