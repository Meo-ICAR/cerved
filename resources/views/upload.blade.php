<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">PDF Upload Test</h1>
        
        <form id="uploadForm" class="space-y-4">
            <div>
                <label for="piva" class="block text-sm font-medium text-gray-700">PIVA</label>
                <input type="text" 
                       id="piva" 
                       name="piva" 
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">PDF File</label>
                <input type="file" 
                       id="file" 
                       name="file" 
                       accept=".pdf" 
                       required
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100">
            </div>
            
            <div>
                <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                <input type="password" 
                       id="api_key" 
                       name="api_key" 
                       required
                       placeholder="Enter your API key"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Upload PDF
            </button>
        </form>
        
        <div id="response" class="mt-6 p-4 bg-gray-50 rounded-md hidden">
            <h3 class="font-medium">Response:</h3>
            <pre id="responseContent" class="mt-2 p-2 bg-white border rounded text-sm overflow-auto"></pre>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('piva', document.getElementById('piva').value);
            formData.append('file', document.getElementById('file').files[0]);
            
            const responseDiv = document.getElementById('response');
            const responseContent = document.getElementById('responseContent');
            
            try {
                const response = await fetch('/api/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Api-Key': document.getElementById('api_key').value,
                    },
                });
                
                const data = await response.json();
                
                responseContent.textContent = JSON.stringify(data, null, 2);
                responseDiv.classList.remove('hidden');
                
                if (response.ok) {
                    responseDiv.className = 'mt-6 p-4 bg-green-50 rounded-md';
                    
                    // If upload was successful and we have a URL, create a link to the file
                    if (data.data && data.data.url) {
                        // Ensure the URL is absolute
                        let fileUrl = data.data.url;
                        if (!fileUrl.startsWith('http')) {
                            // If it's a relative URL, make it absolute using the current origin
                            fileUrl = window.location.origin + (fileUrl.startsWith('/') ? '' : '/') + fileUrl;
                        }
                        
                        const link = document.createElement('a');
                        link.href = fileUrl;
                        link.textContent = 'View Uploaded File';
                        link.className = 'mt-2 inline-block text-indigo-600 hover:text-indigo-800';
                        link.target = '_blank';
                        
                        // Clear any existing link
                        const existingLink = document.getElementById('fileLink');
                        if (existingLink) {
                            existingLink.remove();
                        }
                        
                        link.id = 'fileLink';
                        responseDiv.appendChild(document.createElement('br'));
                        responseDiv.appendChild(link);
                        
                        // Also show the direct URL for debugging
                        const urlInfo = document.createElement('p');
                        urlInfo.textContent = 'File URL: ' + fileUrl;
                        urlInfo.className = 'mt-2 text-sm text-gray-600';
                        responseDiv.appendChild(document.createElement('br'));
                        responseDiv.appendChild(urlInfo);
                    }
                } else {
                    responseDiv.className = 'mt-6 p-4 bg-red-50 rounded-md';
                }
                
            } catch (error) {
                responseContent.textContent = 'Error: ' + error.message;
                responseDiv.className = 'mt-6 p-4 bg-red-50 rounded-md';
                responseDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
