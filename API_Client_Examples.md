# UServe API Client Examples

This file contains example code for consuming the UServe API in different programming languages.

## JavaScript/Node.js Example

### Find Services
```javascript
async function findServices(options = {}) {
    const params = new URLSearchParams();
    
    if (options.search) params.append('search', options.search);
    if (options.categoryId) params.append('category_id', options.categoryId);
    if (options.sort) params.append('sort', options.sort);
    if (options.availableOnly !== undefined) params.append('available_only', options.availableOnly);
    if (options.perPage) params.append('per_page', options.perPage);
    if (options.page) params.append('page', options.page);
    
    const url = `https://your-domain.com/api/v1/services?${params.toString()}`;
    
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return {
                services: data.data,
                pagination: data.pagination,
                filters: data.filters
            };
        } else {
            throw new Error(data.message || 'Failed to fetch services');
        }
    } catch (error) {
        console.error('Error fetching services:', error);
        throw error;
    }
}

// Usage examples
findServices({ search: 'math', categoryId: 1, sort: 'rating' });
findServices({ availableOnly: true, perPage: 20, page: 1 });
```

### Get Service Details
```javascript
async function getServiceDetails(serviceId) {
    const url = `https://your-domain.com/api/v1/services/${serviceId}`;
    
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.data;
        } else {
            throw new Error(data.message || 'Service not found');
        }
    } catch (error) {
        console.error('Error fetching service details:', error);
        throw error;
    }
}

// Usage
getServiceDetails(123);
```

### Get Categories
```javascript
async function getCategories() {
    const url = 'https://your-domain.com/api/v1/categories';
    
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.data;
        } else {
            throw new Error(data.message || 'Failed to fetch categories');
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
        throw error;
    }
}
```

## Python Example

```python
import requests
from typing import Optional, Dict, Any

class UServeAPI:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.session = requests.Session()
        self.session.headers.update({
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        })
    
    def find_services(self, search: Optional[str] = None, 
                     category_id: Optional[int] = None,
                     sort: str = 'newest',
                     available_only: Optional[bool] = None,
                     per_page: int = 15,
                     page: int = 1) -> Dict[str, Any]:
        """Find services with optional filtering"""
        params = {'sort': sort, 'per_page': per_page, 'page': page}
        
        if search:
            params['search'] = search
        if category_id:
            params['category_id'] = category_id
        if available_only is not None:
            params['available_only'] = available_only
            
        response = self.session.get(f"{self.base_url}/api/v1/services", params=params)
        response.raise_for_status()
        
        data = response.json()
        if not data['success']:
            raise Exception(data['message'])
            
        return {
            'services': data['data'],
            'pagination': data['pagination'],
            'filters': data['filters']
        }
    
    def get_service_details(self, service_id: int) -> Dict[str, Any]:
        """Get detailed information about a specific service"""
        response = self.session.get(f"{self.base_url}/api/v1/services/{service_id}")
        response.raise_for_status()
        
        data = response.json()
        if not data['success']:
            raise Exception(data['message'])
            
        return data['data']
    
    def get_categories(self) -> list:
        """Get all service categories"""
        response = self.session.get(f"{self.base_url}/api/v1/categories")
        response.raise_for_status()
        
        data = response.json()
        if not data['success']:
            raise Exception(data['message'])
            
        return data['data']

# Usage example
api = UServeAPI('https://your-domain.com')

# Find services
services = api.find_services(search='math', category_id=1, sort='rating')
print(f"Found {len(services['services'])} services")

# Get service details
service_detail = api.get_service_details(123)
print(f"Service: {service_detail['title']}")

# Get categories
categories = api.get_categories()
print(f"Available categories: {[cat['name'] for cat in categories]}")
```

## React Native Example

```javascript
// services/apiService.js
const API_BASE_URL = 'https://your-domain.com/api/v1';

class ApiService {
    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    async findServices(filters = {}) {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== undefined && value !== null) {
                params.append(key, value);
            }
        });
        
        const endpoint = `/services?${params.toString()}`;
        const response = await this.request(endpoint);
        
        return {
            services: response.data,
            pagination: response.pagination,
            filters: response.filters
        };
    }

    async getServiceDetails(serviceId) {
        const response = await this.request(`/services/${serviceId}`);
        return response.data;
    }

    async getCategories() {
        const response = await this.request('/categories');
        return response.data;
    }
}

export default new ApiService();
```

```javascript
// hooks/useServices.js
import { useState, useEffect } from 'react';
import ApiService from '../services/apiService';

export const useServices = (filters = {}) => {
    const [services, setServices] = useState([]);
    const [pagination, setPagination] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchServices = async (newFilters = filters) => {
        try {
            setLoading(true);
            setError(null);
            const result = await ApiService.findServices(newFilters);
            setServices(result.services);
            setPagination(result.pagination);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchServices();
    }, []);

    return {
        services,
        pagination,
        loading,
        error,
        refetch: fetchServices
    };
};

export const useServiceDetails = (serviceId) => {
    const [service, setService] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchService = async () => {
            try {
                setLoading(true);
                setError(null);
                const result = await ApiService.getServiceDetails(serviceId);
                setService(result);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        if (serviceId) {
            fetchService();
        }
    }, [serviceId]);

    return { service, loading, error };
};
```

## PHP Example (for other PHP applications)

```php
<?php

class UServeApiClient
{
    private $baseUrl;
    private $httpClient;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new GuzzleHttp\Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function findServices(array $filters = []): array
    {
        $query = http_build_query($filters);
        $url = $this->baseUrl . '/api/v1/services?' . $query;
        
        $response = $this->httpClient->get($url);
        $data = json_decode($response->getBody(), true);
        
        if (!$data['success']) {
            throw new Exception($data['message']);
        }
        
        return [
            'services' => $data['data'],
            'pagination' => $data['pagination'],
            'filters' => $data['filters']
        ];
    }

    public function getServiceDetails(int $serviceId): array
    {
        $url = $this->baseUrl . "/api/v1/services/{$serviceId}";
        
        $response = $this->httpClient->get($url);
        $data = json_decode($response->getBody(), true);
        
        if (!$data['success']) {
            throw new Exception($data['message']);
        }
        
        return $data['data'];
    }

    public function getCategories(): array
    {
        $url = $this->baseUrl . '/api/v1/categories';
        
        $response = $this->httpClient->get($url);
        $data = json_decode($response->getBody(), true);
        
        if (!$data['success']) {
            throw new Exception($data['message']);
        }
        
        return $data['data'];
    }
}

// Usage
$api = new UServeApiClient('https://your-domain.com');

// Find services
$result = $api->findServices(['search' => 'math', 'category_id' => 1]);
echo "Found " . count($result['services']) . " services\n";

// Get service details
$service = $api->getServiceDetails(123);
echo "Service: " . $service['title'] . "\n";
```
