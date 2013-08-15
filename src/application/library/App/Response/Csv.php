<?php

namespace App\Response;

class Csv extends AbstractResponse
{
    /**
     * @var bool Output header row?
     */
    protected $headers = true;

    /**
     * Send the response
     * @param array $records
     */
    public function send(array $records)
    {
        $response = $this->di->get('response');

        // Headers for a CSV
        $response->setHeader('Content-type', 'application/csv');

        // By default, filename is just a timestamp. You should probably change this.
        $response->setHeader('Content-Disposition', 'attachment; filename="' . time() . '.csv"');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        // We write directly to out, which means we don't ever save this file to disk.
        $handle = fopen('php://output', 'w');

        // The keys of the first result record will be the first line of the CSV (headers)
        if ($this->headers) {
            fputcsv($handle, array_keys($records[0]));
        }

        // Write each record as a csv line.
        foreach ($records as $line) {
            fputcsv($handle, $line);
        }

        fclose($handle);

        return $this;
    }

    public function useHeaderRow($headers)
    {
        $this->headers = (bool) $headers;
        return $this;
    }
}
