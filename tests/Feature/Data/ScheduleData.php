<?php

namespace Tests\Feature\Data;


use Illuminate\Support\Carbon;

class ScheduleData
{
    public static function get()
    {
        $data = [
            "id" => "afd9345c-f014-4964-8906-66a28c4d8c79",
            "validUntil" => Carbon::now()->addMinutes(15)->toJSON(),
            "legs" => [
                [
                    "id" => "c084df5f-3d4f-4d67-8cb1-05d23f3ad737",
                    "routeInfo" => [
                        "id" => "5cec90eb-f112-4a5a-b8cf-d482c4a23d05",
                        "from" => [
                            "id" => "9ed4e3a2-ea5f-4dbe-bc28-c667da719a39",
                            "name" => "Jupiter"
                        ],
                        "to" => [
                            "id" => "73553ad5-e315-4660-ad2c-5a12ee45cc52",
                            "name" => "Mars"
                        ],
                        "distance" => 550390000
                    ],
                    "providers" => [
                        [
                            "id" => "08524536-369e-4b24-b303-b634944e4577",
                            "company" => [
                                "id" => "acb1ae36-efe0-428d-824e-1a382c5ca02f",
                                "name" => "Space Piper"
                            ],
                            "price" => 72977.21,
                            "flightStart" => Carbon::now()->addMinutes(30)->toJSON(),
                            "flightEnd" => Carbon::now()->addDays(30)->toJSON()
                        ],
                        [
                            "id" => "e6b73bf4-0afd-454b-be11-dcac73022c02",
                            "company" => [
                                "id" => "acb1ae36-efe0-428d-824e-1a382c5ca02f",
                                "name" => "Space Piper"
                            ],
                            "price" => 95917.02,
                            "flightStart" => Carbon::now()->addHours(3)->toJSON(),
                            "flightEnd" => Carbon::now()->addDays(2)->toJSON()
                        ],
                        [
                            "id" => "0e809008-e9e2-43bd-8bd2-25da7c97eb84",
                            "company" => [
                                "id" => "e05ecd0a-57b7-4a39-b72e-66b1f506aaa8",
                                "name" => "Explore Dynamite"
                            ],
                            "price" => 103302.13,
                            "flightStart" => Carbon::now()->addHours(30)->toJSON(),
                            "flightEnd" => Carbon::now()->addDays(3)->toJSON()
                        ]
                    ]
                ],
                [
                    "id" => "2a68f13d-7a0b-4560-9961-7677d3fd1f27",
                    "routeInfo" => [
                        "id" => "dded01d0-30f3-49f5-a397-4bf5ec1f2048",
                        "from" => [
                            "id" => "3255e16d-e74b-49fd-8859-7e5328f4cce0",
                            "name" => "Mars"
                        ],
                        "to" => [
                            "id" => "dd7eb7ec-3e6c-44f7-be6e-7f46201c3519",
                            "name" => "Venus"
                        ],
                        "distance" => 119740000
                    ],
                    "providers" => [
                        [
                            "id" => "04dff386-dd9e-4b2c-a63d-5233046933df",
                            "company" => [
                                "id" => "b5d6df98-50d8-4681-9bbc-dacc73d0d20e",
                                "name" => "Spacelux"
                            ],
                            "price" => 80669.04,
                            "flightStart" => Carbon::now()->addMinutes(10)->toJSON(),
                            "flightEnd" => Carbon::now()->addDay()->toJSON(),
                        ],
                        [
                            "id" => "67d2025e-f979-4b84-b1db-98cd69d39fe5",
                            "company" => [
                                "id" => "acb1ae36-efe0-428d-824e-1a382c5ca02f",
                                "name" => "Space Piper"
                            ],
                            "price" => 100485.1,
                            "flightStart" => Carbon::now()->addDays(4)->toJSON(),
                            "flightEnd" => Carbon::now()->addDays(5)->addHours(12)->toJSON(),
                        ],
                        [
                            "id" => "f7aa85d0-de82-47bc-9823-d8092b55270c",
                            "company" => [
                                "id" => "74d102ce-887a-4538-b44c-0760862bbc1f",
                                "name" => "Explore Origin"
                            ],
                            "price" => 67768.98,
                            "flightStart" => Carbon::now()->addDays(5)->toJSON(),
                            "flightEnd" => Carbon::now()->addDays(6)->toJSON(),
                        ],
                    ]
                ],
                [
                    "id" => "8ea52927-f93d-47a1-b612-be102d6dafa6",
                    "routeInfo" => [
                        "id" => "bfdda9ca-dbba-4591-9ef4-af257780625d",
                        "from" => [
                            "id" => "7951452d-9038-46c4-9daa-e314b265f315",
                            "name" => "Jupiter"
                        ],
                        "to" => [
                            "id" => "452045fa-5e5b-4160-ac2c-6555fcf33245",
                            "name" => "Venus"
                        ],
                        "distance" => 67013000,
                    ],
                    "providers" => [
                        [
                            "id" => "08524536-369e-1324-b303-b634944e4577",
                            "company" => [
                                "id" => "b5d6df98-50d8-4681-9bbc-dacc73d0d20e",
                                "name" => "Spacelux"
                            ],
                            "price" => 55917.02,
                            "flightStart" => Carbon::now()->addHour()->toJSON(),
                            "flightEnd" => Carbon::now()->addHours(30)->toJSON()
                        ],
                        [
                            "id" => "e6b73bf4-12fd-454b-be11-dcac73022c02",
                            "company" => [
                                "id" => "acb1ae36-efe0-428d-824e-1a382c5ca02f",
                                "name" => "Space Piper"
                            ],
                            "price" => 32977.21,
                            "flightStart" => Carbon::now()->addHour(30)->toJSON(),
                            "flightEnd" => Carbon::now()->addHours(60)->toJSON()
                        ],
                        [
                            "id" => "0e129008-e9e2-43bd-8bd2-25da7c97eb84",
                            "company" => [
                                "id" => "f00d10f7-47d2-4214-828b-38af15b84b26",
                                "name" => "Space Voyager"
                            ],
                            "price" => 153302.13,
                            "flightStart" => Carbon::now()->addMinutes(-3)->toJSON(),
                            "flightEnd" => Carbon::now()->addHours(30)->toJSON()
                        ]
                    ]
                ]
            ]
        ];


        return $data;

    }
}
