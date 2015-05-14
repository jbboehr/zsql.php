<?php

namespace zsql\Tests;

class ModelTest extends Common
{

    public function testFind()
    {
        $model = $this->fixtureModelOneFactory();

        $row = $model->find(1);

        $this->assertInstanceOf('\\stdClass', $row);
    }

    public function testFindThrowsWithNoPrimaryKey()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->find(1);
    }

    public function testFindMany()
    {
        $model = $this->fixtureModelOneFactory();

        $rows = $model->findMany(array(1, 2));

        $this->assertCount(2, $rows);
    }

    public function testFindManyThrowsWithNoPrimaryKey()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->findMany(array(1));
    }

    public function testGetDatabase()
    {
        $model = $this->fixtureModelOneFactory();

        $this->assertInstanceOf('\\zsql\\Database', $model->getDatabase());
    }

    public function testSetDatabase()
    {
        $model = $this->fixtureModelOneFactory();

        $database1 = $model->getDatabase();
        $database2 = $this->databaseFactory();

        $model->setDatabase($database2);

        $this->assertEquals(true, $database2 === $model->getDatabase());
        $this->assertEquals(false, $database1 === $model->getDatabase());
    }

    public function testGetTableName()
    {
        $model = $this->fixtureModelOneFactory();

        $this->assertEquals('fixture1', $model->getTableName());
    }

    public function testSetTableName()
    {
        $model = $this->fixtureModelOneFactory();
        $original = $model->getTableName();
        $expected = 'blah';

        $model->setTableName($expected);

        $this->assertEquals($expected, $model->getTableName());
        $this->assertNotEquals($original, $model->getTableName());
    }

    public function testGetPrimaryKey()
    {
        $model = $this->fixtureModelOneFactory();

        $this->assertEquals('id', $model->getPrimaryKey());
    }

    public function testSetPrimaryKey()
    {
        $model = $this->fixtureModelOneFactory();
        $original = $model->getPrimaryKey();
        $expected = 'double';

        $model->setPrimaryKey($expected);

        $this->assertEquals($expected, $model->getPrimaryKey());
        $this->assertNotEquals($original, $model->getPrimaryKey());
    }

    public function testSelect()
    {
        $model = $this->fixtureModelOneFactory();
        $class = '\\zsql\\Select';
        $query = $model->select();
        $string = (string) $query;

        $this->assertInstanceOf($class, $query);
        $this->assertContains($model->getTableName(), $string);
        $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
    }

    public function testSelectWithResultClass()
    {
        $model = new Fixture\ModelWithResultClass($this->databaseFactory());

        $row = $model->select()
            ->limit(1)
            ->query()
            ->fetchRow();

        $this->assertInstanceOf('\\zsql\\Tests\\Fixture\\Result', $row);
    }

    public function testSelectThrowsWithNoTable()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->select();
    }

    public function testInsert()
    {
        $model = $this->fixtureModelOneFactory();
        $class = '\\zsql\\Insert';
        $query = $model->insert()->value('double', '2');
        $string = (string) $query;

        $this->assertInstanceOf($class, $query);
        $this->assertContains($model->getTableName(), $string);
        $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
    }

    public function testInsertThrowsWithNoTable()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->insert();
    }

    public function testUpdate()
    {
        $model = $this->fixtureModelOneFactory();
        $class = '\\zsql\\Update';
        $query = $model->update()->value('double', '2')->where('id', 54);
        $string = (string) $query;

        $this->assertInstanceOf($class, $query);
        $this->assertContains($model->getTableName(), $string);
        $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
    }

    public function testUpdateThrowsWithNoTable()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->update();
    }

    public function testDelete()
    {
        $model = $this->fixtureModelOneFactory();
        $class = '\\zsql\\Delete';
        $query = $model->delete()->where('id', 54);
        $string = (string) $query;

        $this->assertInstanceOf($class, $query);
        $this->assertContains($model->getTableName(), $string);
        $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
    }

    public function testDeleteThrowsWithNoTable()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $model = new Fixture\ModelWithoutTableOrPrimaryKey($this->databaseFactory());
        $model->delete();
    }
}
