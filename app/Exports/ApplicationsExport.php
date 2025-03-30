<?php
namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicationsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Retrieve all applications from the database.
     */
    public function collection()
    {
        return Application::with('user', 'job')->get();
    }

    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return [
            'Nom',
            'Email',
            'Poste',
            'CV Path',
            'Date de candidature'
        ];
    }

    /**
     * Format the data for export.
     */
    public function map($application): array
    {
        return [
            $application->user->name,
            $application->user->email,
            $application->job->name,
            $application->cv_path,
            $application->created_at->format('Y-m-d H:i:s'),
        ];
    }
}