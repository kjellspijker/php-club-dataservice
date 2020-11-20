<?php

namespace PendoNL\ClubDataservice;

class Team extends AbstractItem
{

    /** @var array $competitions */
    private $competitions = [];

    /** @var array $players */
    private $players = [];

    /**
     * All competitions where this team is active in.
     *
     * @return array
     */
    public function getCompetitions()
    {
        if (count($this->competitions) != 0)
        {
            return $this->competitions;
        }

        foreach ($this->api->getClub()->getCompetitions() as $competition)
        {
            if ($competition->teamcode == $this->teamcode)
            {
                $this->competitions[] = $competition;
            }
        }

        return $this->competitions;
    }

    /**
     * All non-redacted players of the team
     *
     * @param array $arguments
     *
     * @return array
     * @throws \PendoNL\ClubDataservice\Exception\InvalidResponseException
     */
    public function getTeamLayout($arguments = [])
    {
        $response = $this->api->request(
            'team-indeling',
            array_merge(
                [
                    'teamcode'       => $this->teamcode,
                    'lokaleteamcode' => -1,
                    'teampersoonrol' => 'ALLES',
                    'toonlidfoto'    => 'NEE',
                ],
                $arguments
            )
        );

        foreach ($response as $item)
        {
            $player = new Player($this->api, $item);

            if ($player->naam !== 'Afgeschermd' && ! array_key_exists($player->relatiecode, $this->players))
            {
                $this->players[$player->relatiecode] = $player;
            }
        }

        return $this->players;
    }
}
