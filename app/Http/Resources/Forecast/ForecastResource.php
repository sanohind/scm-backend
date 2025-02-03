<?php

namespace App\Http\Resources\Forecast;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ForecastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'forecast_id' => $this->forecast_id,
            'bp_code' => $this->bp_code,
            'description' => $this->description,
            'file' => $this->chopStr(),
            'upload_at' => $this->upload_at,
        ];
    }

    // only return the filename
    private function chopStr()
    {
        $string = $this->file;
        $chopFile = Str::chopStart($string, '/storage/forecast/');

        return $chopFile;
    }
}
