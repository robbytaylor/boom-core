<?php

namespace BoomCMS\Http\Controllers\CMS\People\Person;

use BoomCMS\Http\Controllers\CMS\People\PeopleManager;
use BoomCMS\Support\Facades\Person;
use Illuminate\Http\Request;

class BasePerson extends PeopleManager
{
    /**
     * @var string Directory where the views which relate to this class are held.
     */
    protected $viewPrefix = 'boomcms::person.';

    /**
     * Person object to be edited.
     *
     * **CAUTION**
     *
     * [Boom_Controller::before()] sets a person property which is the logged in person.
     * YOU DON'T WANT TO USE THE WRONG PROPERTY.
     *
     * @var Model_Person
     */
    public $editPerson;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->editPerson = Person::find($this->request->route()->getParameter('id'));
    }
}
