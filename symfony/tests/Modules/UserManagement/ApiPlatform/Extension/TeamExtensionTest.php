<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform\Extension;

use App\Tests\Helpers\APITestCase;

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

    public function testItReturnsTeamsTheUserIsMemberOf(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $secondUser = $this->createUser('second@test.com', 'second', 'user');

        $secondUserId = json_decode($secondUser->getContent(), true)['data']['createUser']['user']['id'];

        //first user creates a team and adds a user
        $createTeamMutation = <<<'GQL'
            mutation create($name: String!) {
                createTeam(input: { name: $name }) {
                    team {
                      id
                    }
                }
            }
        GQL;

        //first user creates a team
        $addUserMutation = <<<'GQL'
            mutation add($team: ID!, $user: ID!) {
                addMemberToTeam(input: { team: $team, user: $user }) {
                    clientMutationId
                }
            }
        GQL;

        $this->login('first@test.com');
        $firstTeam = $this->graphql($createTeamMutation, ['name' => 'First team1']);
        $secondTeam = $this->graphql($createTeamMutation, ['name' => 'First team2']);
        $firstTeamId = json_decode($firstTeam->getContent(), true)['data']['createTeam']['team']['id'];
        $secondTeamId = json_decode($secondTeam->getContent(), true)['data']['createTeam']['team']['id'];
        $this->graphql($addUserMutation, ['team' => $firstTeamId, 'user' => $secondUserId]);

        $this->logout();

        //get team with second user
        $listTeamsQuery = <<<'GQL'
            query getTeam($id: ID!) {
                team(id: $id) {
                   id, 
                   name
                }
            }
        GQL;

        $this->login('second@test.com');
        $this->graphql($listTeamsQuery, ['id' => $firstTeamId]);

        $this->assertJsonContains([
            'data' => [
                'team' => [
                    'id' => $firstTeamId,
                    'name' => 'First team1',
                ]
            ]
        ]);

        $this->graphql($listTeamsQuery, ['id' => $secondTeamId]);
        $this->assertJsonContains(['data' => ['team' => null]]);
    }
}
