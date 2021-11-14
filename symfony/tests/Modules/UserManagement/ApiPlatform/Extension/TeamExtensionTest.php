<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform\Extension;

use App\Tests\APITestCase;

class TeamExtensionTest extends APITestCase
{
    public function testItAllowsToSeeOnlyTeamsCreatedByTheUser(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $this->createUser('second@test.com', 'second', 'user');

        // each user creates a team
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

        $this->login('first@test.com');
        $response = $this->graphql($createTeamMutation, ['name' => 'First team1']);
        $this->logout();

        $teamFirstUser = json_decode($response->getContent(), true)['data']['createTeam']['team']['id'];

        $this->login('second@test.com');
        $response = $this->graphql($createTeamMutation, ['name' => 'Second team1']);
        $this->logout();

        $teamSecondUser = json_decode($response->getContent(), true)['data']['createTeam']['team']['id'];

        // each user should only be able to query his own team
        $getTeamQuery = <<<'QGL'
            query getTeam($id: ID!) {
                team(id: $id) {
                    name
                }
            }
        QGL;

        $this->login('first@test.com');
        $this->graphql($getTeamQuery, ['id' => $teamFirstUser]);
        $this->assertJsonContains(['data' => ['team' => ['name' => 'First team1']]]);
        $this->graphql($getTeamQuery, ['id' => $teamSecondUser]);
        $this->assertJsonEquals(['data' => ['team' => null]]);


        $this->login('second@test.com');
        $this->graphql($getTeamQuery, ['id' => $teamSecondUser]);
        $this->assertJsonContains(['data' => ['team' => ['name' => 'Second team1']]]);
        $this->graphql($getTeamQuery, ['id' => $teamFirstUser]);
        $this->assertJsonEquals(['data' => ['team' => null]]);
    }

    public function testItReturnsOnlyTeamsCreatedByTheUser(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $this->createUser('second@test.com', 'second', 'user');

        // each user creates a team
        $createTeamMutation = <<<'GQL'
            mutation create($name: String!) {
                createTeam(input: { name: $name }) {
                    team {
                      id
                    }
                }
            }
        GQL;

        $this->login('first@test.com');
        $this->graphql($createTeamMutation, ['name' => 'First team1']);
        sleep(1);
        $this->graphql($createTeamMutation, ['name' => 'First team2']);
        sleep(1);
        $this->graphql($createTeamMutation, ['name' => 'First team3']);
        $this->logout();

        $this->login('second@test.com');
        $this->graphql($createTeamMutation, ['name' => 'Second team1']);
        sleep(1);
        $this->graphql($createTeamMutation, ['name' => 'Second team2']);
        sleep(1);
        $this->graphql($createTeamMutation, ['name' => 'Second team3']);
        $this->logout();

        //List first users teams, should include only his teams;
        $listTeamsQuery = <<<GQL
            query listTeams {
                teams {
                    edges {
                        node {
                            id
                            name,
                            createdAt
                        }
                    }
                }
            }
        GQL;

        $this->login('first@test.com');
        $response = $this->graphql($listTeamsQuery);
        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(3, count($data['teams']['edges']));
        $this->assertJsonContains([
            'data' => [
                'teams' => [
                    'edges' => [
                        ['node' => ['name' => 'First team3']],
                        ['node' => ['name' => 'First team2']],
                        ['node' => ['name' => 'First team1']],
                    ]
                ]
            ]
        ]);

        $this->logout();
        $this->login('second@test.com');
        $response = $this->graphql($listTeamsQuery);
        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(3, count($data['teams']['edges']));
        $this->assertJsonContains([
            'data' => [
                'teams' => [
                    'edges' => [
                        ['node' => ['name' => 'Second team3']],
                        ['node' => ['name' => 'Second team2']],
                        ['node' => ['name' => 'Second team1']],
                    ]
                ]
            ]
        ]);
    }
}
