<?php

namespace Kinopoisk\RatingBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Kinopoisk\RatingBundle\Model\ParseKinopoiskRatingModel;

class ParseKinopoiskRatingModelTest extends WebTestCase
{
    protected function getFileContent()
    {
        $filename = '/test_html/page.html';
        $dir = dirname(__DIR__);
        if (file_exists($dir . $filename)) {
            return file_get_contents($dir . $filename);
        }

        return '';
    }

    protected function parseTr()
    {
        $content = $this->getFileContent();
        if ($content) {
            return ParseKinopoiskRatingModel::getCrowlerTr($content);
        } else {
            $this->assertTrue(false);
        }

        return '';
    }

    /**
     * ��������� ������� �������
     */
    public function testParsePositions()
    {
        $tr = $this->parseTr();
        if ($tr) {
            $positions = ParseKinopoiskRatingModel::parsePositions($tr);
            $this->assertEquals(range(1, 10), $positions);
        }
    }

    /**
     * ��������� ������� �������
     */
    public function testParseVotes()
    {
        $tr = $this->parseTr();
        if ($tr) {
            $votes = ParseKinopoiskRatingModel::parseVotes($tr);
            //print_r($votes);
            $check = array(
                181682,
                173566,
                175920,
                101542,
                84676,
                144243,
                121109,
                195884,
                162460,
                135233
            );
            $this->assertEquals($check, $votes);
        }
    }

    /**
     * ��������� ������� ���������
     */
    public function testParseRatings()
    {
        $tr = $this->parseTr();
        if ($tr) {
            $items = ParseKinopoiskRatingModel::parseRatings($tr);
            $check = array(
                9.205,
                9.149,
                9.030,
                8.923,
                8.854,
                8.822,
                8.801,
                8.788,
                8.747,
                8.720,
            );
            $this->assertEquals($check, $items);
        }
    }

    /**
     * ��������� ������� ����
     */
    public function testParseNames()
    {
        $tr = $this->parseTr();
        if ($tr) {
            $items = ParseKinopoiskRatingModel::parseNames($tr);
            $check = array(
                '����� �� �������� (1994)',
                '������� ���� (1999)',
                '������� ���� (1994)',
                '1+1 (2011)',
                '������ �������� (1993)',
                '���� (1994)',
                '������ ��� (1994)',
                '������ (2010)',
                '���������� ���� (1999)',
                '���� ���������� ������ ��������� (1973)'
            );
            foreach ($check as &$ch) {
                $ch = utf8_encode($ch);
            }


            $this->assertEquals($check, $items);
        }
    }

    /**
     * ��������� ������� ����
     */
    public function testParseYears()
    {
        $name = '����������� (2323)';
        $year = ParseKinopoiskRatingModel::parseYear($name);
        $this->assertEquals('2323', $year);
    }


}
