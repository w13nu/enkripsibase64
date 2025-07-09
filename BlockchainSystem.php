<?php
class Block {
    public $timestamp;
    public $fileHash;
    public $previousHash;
    public $merkleRoot;
    public $fileName;
    public $fileType;
    public $encryptionType;
    public $nonce;
    
    public function __construct($fileData, $fileName, $fileType, $previousHash = '') {
        $this->timestamp = date('Y-m-d H:i:s');
        $this->fileHash = hash('sha256', $fileData);
        $this->previousHash = $previousHash;
        $this->fileName = $fileName;
        $this->fileType = $fileType;
        $this->encryptionType = "Caesar-Vigenere-AES-XOR-GCM";
        $this->nonce = 0;
        $this->merkleRoot = $this->calculateMerkleRoot([$this->fileHash]);
    }
    
    public function calculateHash() {
        return hash('sha256', 
            $this->previousHash . 
            $this->merkleRoot . 
            $this->timestamp . 
            $this->fileName .
            $this->fileType .
            $this->nonce
        );
    }
    
    public function calculateMerkleRoot($hashes) {
        if (count($hashes) == 1) return $hashes[0];
        
        $newHashes = [];
        for ($i = 0; $i < count($hashes) - 1; $i += 2) {
            $newHashes[] = hash('sha256', $hashes[$i] . $hashes[$i + 1]);
        }
        
        if (count($hashes) % 2 == 1) {
            $newHashes[] = hash('sha256', end($hashes) . end($hashes));
        }
        
        return $this->calculateMerkleRoot($newHashes);
    }
    
    public function toArray() {
        return [
            'timestamp' => $this->timestamp,
            'fileHash' => $this->fileHash,
            'previousHash' => $this->previousHash,
            'merkleRoot' => $this->merkleRoot,
            'fileName' => $this->fileName,
            'fileType' => $this->fileType,
            'encryptionType' => $this->encryptionType,
            'blockHash' => $this->calculateHash()
        ];
    }
}

class BlockchainLogger {
    private $logFile;
    private $chain;
    
    public function __construct() {
        date_default_timezone_set('Asia/Jakarta');
        $this->logFile = 'logs/blockchain_log.json';
        $this->ensureLogDirectory();
        $this->chain = $this->loadChain();
    }
    
    public function getBlockByFileName($fileName) {
        foreach ($this->chain as $block) {
            if ($block['fileName'] === $fileName) {
                return $block;
            }
        }
        return null;  // Kembalikan null jika tidak ditemukan
    }

    // Tambahkan metode ini untuk konsistensi dengan pemanggilan di delete_record.php
    public function deleteBlockByFileHash($fileHash) {
        return $this->deleteRecord($fileHash);
    }

    public function deleteRecord($fileHash) {
        $chain = $this->getChain();
        
        // Find the index of the record to delete
        $indexToDelete = -1;
        foreach ($chain as $index => $block) {
            if ($block['fileHash'] === $fileHash) {
                $indexToDelete = $index;
                break;
            }
        }
        
        if ($indexToDelete === -1) {
            return false; // Record not found
        }
        
        // Remove the record from the chain
        array_splice($chain, $indexToDelete, 1);
        
        // If we're not deleting the first block, update the previous hash references
        if ($indexToDelete < count($chain)) {
            // Update the chain after the deleted record
            $this->recalculateChain($chain, $indexToDelete);
        }
        
        // Save the updated chain
        $this->chain = $chain; // Perbarui chain internal
        $this->saveChain(); // Panggil saveChain tanpa parameter
        
        return true; // Kembalikan true jika berhasil
    }

    private function recalculateChain(&$chain, $startIndex) {
        $previousHash = $startIndex > 0 ? $chain[$startIndex - 1]['blockHash'] : '0';
        
        for ($i = $startIndex; $i < count($chain); $i++) {
            $chain[$i]['previousHash'] = $previousHash;
            $chain[$i]['blockHash'] = $this->calculateBlockHash($chain[$i]);
            $previousHash = $chain[$i]['blockHash'];
        }
    }

    // Perbaiki metode saveChain untuk konsistensi
    private function saveChain() {
        file_put_contents($this->logFile, json_encode($this->chain, JSON_PRETTY_PRINT));
        return true; // Kembalikan true untuk menunjukkan keberhasilan
    }

    private function calculateBlockHash($block) {
        return hash('sha256', 
            $block['previousHash'] . 
            $block['merkleRoot'] . 
            $block['timestamp'] . 
            $block['fileName'] .
            $block['fileType'] .
            ($block['nonce'] ?? 0)
        );
    }

    private function ensureLogDirectory() {
        $dir = dirname($this->logFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    
    private function loadChain() {
        if (file_exists($this->logFile)) {
            $content = file_get_contents($this->logFile);
            return json_decode($content, true) ?: [];
        }
        return [];
    }
    
    public function addBlock($fileData, $fileName, $fileType) {
        $previousHash = empty($this->chain) ? '0' : end($this->chain)['blockHash'];
        $block = new Block($fileData, $fileName, $fileType, $previousHash);
        $blockData = $block->toArray();
        
        $this->chain[] = $blockData;
        $this->saveChain();
        
        return $blockData;
    }
    
    public function getChain() {
        return $this->chain;
    }
    
    public function verifyChain() {
        for ($i = 0; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
    
            // Cek validitas hash dari block itu sendiri
            $calculatedHash = $this->calculateBlockHash($currentBlock);
            if ($currentBlock['blockHash'] !== $calculatedHash) {
                return false;
            }
    
            // Cek previous hash (kecuali untuk block pertama)
            if ($i > 0) {
                $previousBlock = $this->chain[$i - 1];
                if ($currentBlock['previousHash'] !== $previousBlock['blockHash']) {
                    return false;
                }
            }
        }
        return true;
    }
}