<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelData extends Model
{
    use HasFactory;
    protected $fillable = [
        'Date',
        'Time_or_Hour',
        'GenerationUnit1_NozelOpen',
        'GenerationUnit1_GeneratorVoltage_AY',
        'GenerationUnit1_GeneratorVoltage_YB',
        'GenerationUnit1_GeneratorVoltage_BR',
        'GenerationUnit1_GeneratorCurrent_I1',
        'GenerationUnit1_GeneratorCurrent_I2',
        'GenerationUnit1_GeneratorCurrent_I3',
        'GenerationUnit1_GeneratorOutput_KW',
        'GenerationUnit1_GeneratorOutput_kVAr',
        'GenerationUnit1_PF_Close',
        'GenerationUnit1_Frequency_HZ',
        'GenerationUnit1_Energy_kWH',
        'GenerationUnit2_NozelOpen',
        'GenerationUnit2_GeneratorVoltage_AY',
        'GenerationUnit2_GeneratorVoltage_YB',
        'GenerationUnit2_GeneratorVoltage_BR',
        'GenerationUnit2_GeneratorCurrent_I1',
        'GenerationUnit2_GeneratorCurrent_I2',
        'GenerationUnit2_GeneratorCurrent_I3',
        'GenerationUnit2_GeneratorOutput_KW',
        'GenerationUnit2_GeneratorOutput_kVAr',
        'GenerationUnit2_PF_Close',
        'GenerationUnit2_Frequency_HZ',
        'GenerationUnit2_Energy_kWH',
        '_11KVSide_LineVoltage_RY',
        '_11KVSide_LineVoltage_YB',
        '_11KVSide_LineVoltage_BR',
        '_11KVSide_LineCurrent_I1',
        '_11KVSide_LineCurrent_I2',
        '_11KVSide_LineCurrent_I3',
        '_11KVSide_Output_KW',
        '_11KVSide_Output_kVAr',
        '_11KVSide_PF_Close',
        '_11KVSide_Frequency_Hz',
        'MainMeterReading_kWH',
        'CheckMeterReading_kWH',
        'MainMeterUnitIn1hr_kWH',
        'CheckMeterUnitIn1hr_kWH',
        'Remark',
    ];
}
