<?php

namespace AppBundle\Storage;

use AppBundle\Object\Note;

class NoteStorage
{
    private $notes;

    public function addNote(Note $note)
    {
       $this->notes[] = $note;

        return true;
    }

    public function getNotes()
    {
        return $this->notes;
    }
}