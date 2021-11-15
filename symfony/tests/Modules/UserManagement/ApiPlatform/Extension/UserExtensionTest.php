<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform\Extension;

use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use App\Tests\APITestCase;

class UserExtensionTest extends APITestCase
{
    public function testItAllowsToSeeUsersOfSameTeams(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $this->createUser('second@test.com', 'second', 'user');
        $this->createUser('third@test.com', 'third', 'user');

        $expected = [
            ["node" => [
                "email" => "first@test.com"
            ]],
            ["node" => [
                "email" => "second@test.com"
            ]]
        ];
        sort($expected);

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

        // login with each user added to the team, and check they can see the other one
        $listUsersQuery = <<<GQL
            query listUsers {
                users {
                    edges {
                        node {
                            email
                        }
                    }
                }
            }
        GQL;

        $this->login('first@test.com');
        $response = $this->graphql($listUsersQuery);

        $data = json_decode($response->getContent(), true)["data"]["users"]["edges"];
        sort($data);

        $this->assertArraySubset($expected, $data);


        $this->login('second@test.com');
        $response = $this->graphql($listUsersQuery);

        $data = json_decode($response->getContent(), true)["data"]["users"]["edges"];
        sort($data);

        $this->assertArraySubset($expected, $data);
    }


    public function testItAllowsToSeeUsersOfOwnedTeams(): void
    {
        $this->createUser('first@test.com', 'first', 'user');
        $this->createUser('second@test.com', 'second', 'user');
        $this->createUser('third@test.com', 'third', 'user');

        $expected = [
            ["node" => [
                "email" => "first@test.com"
            ]],
            ["node" => [
                "email" => "second@test.com"
            ]]
        ];
        sort($expected);

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

        // login with each user added to the team, and check they can see the other one
        $listUsersQuery = <<<GQL
            query listUsers {
                users {
                    edges {
                        node {
                            email
                        }
                    }
                }
            }
        GQL;

        $this->login('third@test.com');
        $response = $this->graphql($listUsersQuery);

        $data = json_decode($response->getContent(), true)["data"]["users"]["edges"];
        sort($data);

        $this->assertArraySubset($expected, $data);
    }
}
