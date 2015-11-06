"""
This downloads the GO terms from the DNAtabase MySQL tables and produces a tsv
file which can be used for producing the recommendation model.
"""

import mysql_utilities as mu
import csv

def query_go():
    """Query the GO data from MySQL"""
    conn = mu.get_conn()
    cursor = conn.cursor()
    query = ("SELECT gene.gid, gene.symbol "
             "FROM gene "
             "WHERE gene.source = 'GO'")
    cursor.execute(query)
    results = cursor.fetchall()
    write_table(results)

def write_table(results, outfile='../data/go.tsv'):
    """Writes the provided two column format file to disk as a tsv,
    where all values in column 2 which share the same column 1 are
    collpased to one row"""
    
    final_rows = dict()
    for row in results:
        (go, symbol) = row
        if go not in final_rows:
            final_rows[go] = list()
        final_rows[go].append(symbol)

    with open(outfile, 'w') as tsvfile:
        writer = csv.writer(tsvfile, delimiter='\t')
        for go in sorted(final_rows.keys()):
            writer.writerow([go] + sorted(final_rows[go]))

if __name__ == "__main__":
    query_go()
