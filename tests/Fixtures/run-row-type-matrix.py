"""Run isolated package/provider combinations without uninstalling anything.

Usage: python3 run-row-type-matrix.py PHP_EXECUTABLE BOOTSTRAP_FILE
The bootstrap must return a Composer ClassLoader or define $loader, and map local packages.
"""
import itertools
from pathlib import Path
import subprocess
import sys

fixture = str(Path(__file__).with_name('row-type-packages.php'))
php, bootstrap = sys.argv[1:3]
combinations = [
    ('FileCabinet',), ('Operators',), ('Warehouse',),
    ('FileCabinet', 'Operators'), ('Operators', 'FileCabinet'),
    ('FileCabinet', 'Warehouse'), ('Warehouse', 'FileCabinet'),
    *itertools.permutations(('FileCabinet', 'Operators', 'Warehouse')),
]
failed = []
for packages in combinations:
    for cached in (False, True):
        # Resolve early whenever FileCabinet comes first to exercise late contributions.
        command = [php, fixture, bootstrap, ','.join(packages)]
        if cached:
            command.append('--cached')
        if packages[0] == 'FileCabinet':
            command.append('--early')
        result = subprocess.run(command, capture_output=True, text=True)
        passes = [line for line in result.stdout.splitlines() if line.startswith('PASS ')]
        if result.returncode or not passes:
            failed.append(','.join(packages))
            print(result.stdout[-5000:] + result.stderr[-2000:], flush=True)
        else:
            print(passes[-1], flush=True)
if failed:
    sys.exit(f'FAILED: {failed}')
print(f'All {len(combinations) * 2} isolated combinations passed.')
