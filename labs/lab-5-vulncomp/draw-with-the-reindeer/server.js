import express from 'express';
import { promises as fs } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const app = express();
app.use(express.json());
app.use(express.static('static'));

// Function to render Turtle commands into SVG
function renderTurtleSVG(commands) {
    const gridSize = 32; // Grid dimensions
    const cellSize = 256 / gridSize; // Size of each grid cell
    let x = 128; // Start at the center
    let y = 128;
    let penDown = true; // By default, the pen is drawing

    const svgLines = []; // Store lines for the SVG

    commands.forEach((command) => {
        const [cmdRaw, valueRaw] = command.split(/\s+/);
        const value = parseInt(valueRaw || '1', 10); // Default to 1 step
        const isMoveOnly = cmdRaw.startsWith('@'); // Movement-only commands
        const cmd = isMoveOnly ? cmdRaw.substring(1) : cmdRaw; // Remove '@'

        // Calculate movement
        const move = (dx, dy) => {
            const newX = x + dx * cellSize;
            const newY = y + dy * cellSize;

            if (penDown) {
                svgLines.push(
                    `<line x1="${x}" y1="${y}" x2="${newX}" y2="${newY}" stroke="black" stroke-width="2"/>`
                );
            }

            x = newX;
            y = newY;
        };

        // Handle commands
        switch (cmd) {
            case 'up':
                move(0, -value);
                break;
            case 'down':
                move(0, value);
                break;
            case 'left':
                move(-value, 0);
                break;
            case 'right':
                move(value, 0);
                break;
            default:
                throw new Error(`Unknown command: ${cmd}`);
        }

        // If it's a move-only command, toggle pen state
        penDown = !isMoveOnly;
    });

    return `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
            ${svgLines.join('\n')}
        </svg>
    `;
}

app.post('/render', async (req, res) => {
    const { module } = req.body;

    try {
        // Convert the module property into a valid ES module
        const moduleContent = `export default ${module};`;

        // Create a temporary file for the module
        const tempFile = path.join(path.dirname(fileURLToPath(import.meta.url)), `temp-${Date.now()}.mjs`);
        await fs.writeFile(tempFile, moduleContent, 'utf8');

        // Dynamically import the module
        const dynamicModule = await import(tempFile);

        // Clean up the temporary file after import
        await fs.unlink(tempFile);

        // Process the Turtle script
        const commands = dynamicModule.default.script.split(';').map((c) => c.trim());
        const svg = renderTurtleSVG(commands);

        // Return the SVG
        res.type('image/svg+xml').send(svg);
    } catch (err) {
        res.status(500).send('Rendering failed: ' + err.message);
    }
});
// Debug endpoint to return the contents of index.js
app.get('/debug', async (req, res) => {
    try {
        // Resolve the path to the current file
        const filePath = fileURLToPath(import.meta.url);

        // Read the file contents
        const content = await fs.readFile(filePath, 'utf8');

        // Send as plain text
        res.type('text/plain').send(content);
    } catch (err) {
        res.status(500).send('Failed to read file: ' + err.message);
    }
});
app.listen(3000, () => console.log('Turtle Compiler running on port 3000'));
