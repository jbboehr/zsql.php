<?php

namespace zsql\Tests;

class ScannerTest extends Common
{
    public function testIterator()
    {
        foreach( array(1, 2, 3, 10, 15) as $limit ) {
            $database = $this->databaseFactory();
            $scanner = new \zsql\ScannerIterator(
                $database->select()
                    ->from('fixture3')
                    ->order('id', 'ASC')
                    ->limit($limit)
            );

            $count = 0;
            $expected = '';
            $actual = '';
            foreach( $scanner as $row ) {
                $expected .= ++$count . ' ';
                $actual .= $row->id . ' ';
            }
            $this->assertEquals($expected, $actual);
        }
    }

    public function testGenerator()
    {
        if( PHP_VERSION_ID < 50500 || defined('HHVM_VERSION') ) {
            return $this->markTestIncomplete('Generators are not supported on < PHP 5.5 or HHVM');
        }

        foreach( array(1, 2, 10, 15) as $limit ) {
            $database = $this->databaseFactory();
            $scanner = new \zsql\ScannerGenerator(
                $database->select()
                    ->from('fixture3')
                    ->order('id', 'ASC')
                    ->limit($limit)
            );

            $count = 0;
            $expected = '';
            $actual = '';
            foreach( $scanner() as $row ) {
                $expected .= ++$count . ' ';
                $actual .= $row->id . ' ';
            }
            $this->assertEquals($expected, $actual);
        }
    }

    public function testMode()
    {
        $database = $this->databaseFactory();
        $scanner = new \zsql\ScannerIterator(
            $database->select()
                ->from('fixture3')
                ->order('id', 'ASC')
                ->limit(3)
        );
        $scanner->mode(\zsql\Result::FETCH_COLUMN);

        $count = 0;
        $expected = '';
        $actual = '';
        foreach( $scanner as $cursor => $val ) {
            $expected .= $count . ' ' . ++$count . ' ';
            $actual .= $cursor . ' ' . $val . ' ';
        }
        $this->assertEquals($expected, $actual);
    }
}
