<?php

namespace App\Mappings\Processors;

use Illuminate\Support\Facades\Http;
use App\Models\Mapping;

/**
 * Class MFilesMappingProcessor
 *
 * **Retrieves mapping data from M-Files**, a document management system.
 * This processor dynamically queries M-Files using configured credentials and API endpoints.
 *
 * ---
 *
 * ## **Purpose**
 * - Acts as a **data processor** that retrieves values from M-Files **based on a given property code**.
 * - Fetches **specific property values** from a document stored in M-Files.
 * - Provides **seamless integration** between Laravel and M-Files.
 *
 * ---
 *
 * ## **Configuration**
 * - The API credentials and endpoint are **fetched from the config file (`homeful-contracts`)**.
 * - The **M-Files object ID and property IDs** are pre-defined as class constants.
 * - A **fallback default value** is returned if the API call fails.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $mapping = Mapping::factory()->make([
 *     'path' => 'document_name',
 * ]);
 *
 * $processor = new MFilesMappingProcessor($mapping, 'PROPERTY_CODE_123');
 * $value = $processor->process();
 *
 * echo $value; // Outputs the retrieved value or default if the request fails
 * ```
 *
 * ---
 *
 * ## **How it Works**
 *
 * 1. **Retrieves API credentials** from `config('homeful-contracts.mfiles_credentials')`.
 * 2. **Constructs a request payload** with:
 *    - M-Files **object ID**
 *    - **Property code** to fetch
 *    - A **set of predefined property IDs**
 * 3. **Sends an HTTP POST request** to M-Files API.
 * 4. **Extracts the desired property** from the JSON response.
 * 5. **Returns the retrieved value or a default fallback** if the request fails.
 *
 * ---
 *
 * ## **Constants**
 *
 * | Constant | Description |
 * |----------|------------|
 * | `OBJECT_ID` | M-Files object ID for document retrieval. |
 * | `PROPERTY_ID` | Default property ID for lookup. |
 * | `PROPERTY_IDS` | List of additional property IDs to include in the request. |
 *
 * ---
 *
 * ## **Key Features**
 * - ✅ **Fetches data directly from M-Files.**
 * - ✅ **Supports multiple property lookups via `PROPERTY_IDS`.**
 * - ✅ **Handles API authentication dynamically.**
 * - ✅ **Returns a default value when the API request fails.**
 *
 */
class MFilesMappingProcessor extends AbstractMappingProcessor
{
    /** @var int The M-Files object ID used for document retrieval */
    private const OBJECT_ID = 119;

    /** @var int Default property ID */
    private const PROPERTY_ID = 1105;

    /** @var array<int> List of property IDs to fetch */
    private const PROPERTY_IDS = [
        1105, 1050, 1109, 1203, 1204, 1202, 1285, 1024, 1290
    ];

    /** @var string The property code used in the M-Files API request */
    private string $propertyCode;

    /**
     * Constructor to initialize the mapping processor with a property code.
     *
     * @param Mapping $mapping The mapping configuration.
     * @param string $propertyCode The specific property code to retrieve.
     */
    public function __construct(Mapping $mapping, string $propertyCode)
    {
        parent::__construct($mapping);
        $this->propertyCode = $propertyCode;
    }

    /**
     * Fetch the initial value from M-Files API.
     *
     * Retrieves document properties based on the configured M-Files API credentials.
     *
     * @return mixed The retrieved property value or the default fallback value.
     */
    protected function getInitialValue(): mixed
    {
        // Retrieve M-Files API credentials and endpoint from config
        $mfilesLink = config('homeful-contracts.mfiles_link');
        $credentials = config('homeful-contracts.mfiles_credentials');

        // Construct the request payload
        $payload = [
            "Credentials" => [
                "Username" => $credentials['username'],
                "Password" => $credentials['password'],
            ],
            "Documents" => [
                [
                    "objectID" => 119,
                    "propertyID" => 1105,
                    "name" => "PVT3_DEV-01-001-001",
                    "json_name" => config('homeful-contracts.mfiles_mapping.inventory'),
                    "property_ids" => [
                        1202,
                        1105,
                        1050,
                        1109,
                        1203,
                        1204,
                        1202,
                        1285
                    ]
                ],
                [
                    "objectID" => 101,
                    "propertyID" => 1050,
                    "name" => "PPMP",
                    "json_name" => config('homeful-contracts.mfiles_mapping.project'),
                    "property_ids" => [
                        1293,
                        1294
                    ]
                ]
            ]
        ];

        $payload = [
            "Credentials" => [
                "Username" => $credentials['username'],
                "Password" => $credentials['password'],
            ],
            "Documents" => [
                [
                    "objectID" => 119,
                    "propertyID" => 1105,
                    "name" => "PVT3_DEV-01-001-001",
                    "json_name" => "",
                    "property_ids" => [
                        1105,
                        1050,
                        1109,
                        1203,
                        1204,
                        1202,
                        1285
                    ],
                    "mask_field" => [
                        "",
                        "project_code",
                        null,
                        null,
                        null,
                        null,
                        "technical_description"
                    ]
                ],
                [
                    "objectID" => 101,
                    "propertyID" => 1050,
                    "name" => "PPMP",
                    "json_name" => "",
                    "property_ids" => [
                        1293,
                        1294
                    ],
                    "mask_field" => [
                        "project_developer",
                        "registry_of_deed"
                    ]
                ]
            ]
        ];
        // Send HTTP request to M-Files API

        $end_point = "$mfilesLink/api/mfiles/document/search/properties-many";

        $response = Http::post($end_point, $payload);

        // Return the extracted JSON property or the default fallback
        return $response->successful()
            ? $response->json($this->mapping->path)
            : $this->mapping->default;
    }
}
