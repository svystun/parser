<?php namespace App\Services;

use App\Domain;

/**
 * Class ParserService
 * @package App\Services
 */
class ParseJsonService
{
    /**
     * const STATUS_NO_ERROR
     */
    const STATUS_NO_ERROR = 'NOERROR';

    /**
     * const ANSWER_TYPE
     */
    const ANSWER_TYPE = 'A';

    /**
     * @var $json
     */
    protected $json;

    /**
     * Load json file
     *
     * @param string $jsonFileName
     * @throws \Exception
     */
    public function loadJsonFile(string $jsonFileName)
    {
        $content = file(storage_path('app'. DIRECTORY_SEPARATOR . $jsonFileName));
        $this->json = collect();
        foreach($content as $row) {
            if ($jsonRow = json_decode($row, true)) {
                $this->json->push($jsonRow);
                continue;
            }
            throw new \Exception('Json file is corrupted');
        }
    }

    /**
     * Store root domains
     */
    public function storeRootDomains()
    {
        $this->json = $this->json->filter(function ($value) {
            return $value['status'] == self::STATUS_NO_ERROR;
        });

        $insertData = $this->json->map(function ($item) {
            $domain = $this->getRootDomain($item['name']);
            return compact('domain');
        })->unique('domain')->all();

        Domain::insert($insertData);
    }

    /**
     * Store related domains
     */
    public function storeRelatedDomains()
    {
        $rootDomains = Domain::where('parent_domain_id', null)->get();

        $insertData = $this->json->transform(function ($item) use ($rootDomains) {
            $domain = $this->getRootDomain($item['name']);
            $parentId = $rootDomains->first(function ($value) use ($domain) {
                return $value->domain == $domain;
            })->id;

            return $this->parseAnswers($item['data']['answers'], $parentId);
        })->flatten(1)->all();

        Domain::insert($insertData);
    }

    /**
     * Parse and transform answers
     *
     * @param array $answers
     * @param int $parentId
     * @return array
     */
    protected function parseAnswers(array $answers, int $parentId)
    {
        $answers = array_filter($answers, function ($answer) {
            return ($answer['type'] == self::ANSWER_TYPE);
        });

        return array_map(function($answer) use ($parentId) {
            return [
                'domain' => $answer['name'],
                'parent_domain_id' => $parentId,
                'ip' => $answer['answer']
            ];
        }, $answers);
    }

    /**
     * Get root domain by url
     *
     * @param $url
     * @return string
     */
    protected function getRootDomain(string $url)
    {
        $urlParts = explode('.', $url);
        return end($urlParts);
    }

}