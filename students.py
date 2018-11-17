import pyodbc, csv, sys, json
with open(sys.argv[1], 'r') as fh:
	server = 'LAPTOP-DJ46JC9S'
	database = 'voodle'
	username = 'voodle'
	password = 'KanekiK'
	conn = pyodbc.connect('DRIVER={ODBC Driver 17 for SQL Server};SERVER=' + server + ';DATABASE=' + database + ';UID=' + username + ';PWD=' + password)
	cursor = conn.cursor()
	file = csv.reader(fh)
	students = []
	for name in file:
		try:
			temp = int(name[0])
			cursor.execute('SELECT username, LDAP FROM students WHERE username = ? OR LDAP = ?', [name[0], name[0]])
		except:
			cursor.execute('SELECT username, LDAP FROM students WHERE username = ?', [name[0]])
		ldaps = cursor.fetchall()
		if len(ldaps) == 0:
			raise Exception('No student found with the given name/ldap')
		elif len(ldaps) != 1:
			raise Exception('Server Error!')
		students.append({'name': ldaps[0][0], 'ldap': ldaps[0][1]})
	print(json.dumps(list(students)))