<?php

namespace App\Observers;
use App\Models\ContactsUpdateLogs;
use Homeful\Contacts\Models\Contact;
class ContactObserver
{
    /**
     * Handle the Contact "updated" event.
     *
     * @param  Homeful\Contacts\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
        // Get dirty attributes (those that changed)
        $dirtyAttributes = $contact->getDirty();

        foreach ($dirtyAttributes as $attr => $newValue) {
            $originalValue = $contact->getOriginal($attr);

            // Compare and log changes for nested or simple attributes
            $this->logChange($contact, $attr, $originalValue, $newValue);
        }
    }

    /**
     * Compare and log changes for attributes.
     *
     * @param  Contact  $contact
     * @param  string   $attr
     * @param  mixed    $originalValue
     * @param  mixed    $newValue
     * @return void
     */
    private function logChange(Contact $contact, $attr, $originalValue, $newValue)
    {
        // Decode JSON to array if necessary
        $originalValue = $this->normalizeToArray($originalValue);
        $newValue = $this->normalizeToArray($newValue);


        // If values are arrays, flatten them to find and log specific changes
        if (is_array($originalValue) && is_array($newValue)) {
            $this->logArrayChanges($contact, $attr, $originalValue, $newValue);
        } elseif ($originalValue !== $newValue) {
            // Log simple attribute changes
            $this->createLog($contact, $originalValue, $newValue, $attr);
        }
    }

    /**
     * Log changes for array attributes by comparing keys.
     *
     * @param  Contact  $contact
     * @param  string   $attr
     * @param  array    $original
     * @param  array    $new
     * @return void
     */
    private function logArrayChanges(Contact $contact, $attr, array $original, array $new)
    {
        $allKeys = array_unique(array_merge(array_keys($original), array_keys($new)));

        foreach ($allKeys as $key) {
            $origValue = $original[$key] ?? null;
            $newValue = $new[$key] ?? null;

            // Check if both values are arrays
            if (is_array($origValue) && is_array($newValue)) {

                // Recursively log changes for nested arrays
                $this->logArrayChanges($contact, "$attr.$key", $origValue, $newValue);
            } elseif ($origValue !== $newValue) {
//                if($attr=='order'){
//                    dd($allKeys,$original,$new,$origValue,$newValue,"$attr.$key");
//                }
                // Log the specific change
                $this->createLog($contact, $origValue, $newValue, "$attr.$key");
            }
        }
    }


    /**
     * Create a new log entry for a specific change.
     *
     * @param  Contact  $contact
     * @param  mixed    $from
     * @param  mixed    $to
     * @param  string   $field
     * @return void
     */
    private function createLog(Contact $contact, $from, $to, $field)
    {
        if ($field=='updated_at'){
            return ;
        }

        ContactsUpdateLogs::create([
            'contacts_id' => $contact->id,
            'user_id' => auth()->id(),
            'field' => $field,
            'from' => $this->formatValue($from),
            'to' => $this->formatValue($to),
        ]);
    }

    /**
     * Format value for logging.
     *
     * @param  mixed  $value
     * @return string|null
     */
    private function formatValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $value;
    }

    /**
     * Normalize a value to an array.
     *
     * @param mixed $value
     * @return array
     */
    private function normalizeToArray($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }

        return $value;
    }
}
