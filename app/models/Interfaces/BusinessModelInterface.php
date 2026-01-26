<?php
/**
 * Business Model Interface
 * 
 * This interface defines the contract that all business models must implement
 * Ensures consistency across all API versions
 */

interface BusinessModelInterface {
    
    // Core CRUD operations that all versions must support
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getById(int $id);
    public function getAll(int $limit = 10, int $offset = 0, array $filters = []);
    public function countAll(array $filters = []);
    
    // Standard search functionality
    public function search(string $searchTerm, array $filters = [], int $limit = 10, int $offset = 0);
    
    // Business-specific operations
    public function deactivate(int $id, string $reason = '');
    public function reactivate(int $id);
    public function getFeatured(int $limit = 10, int $offset = 0, array $filters = []);
    
    // Utility methods
    public function columnExists(string $columnName);
    public function getTableName();
}
?>