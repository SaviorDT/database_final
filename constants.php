<?php
$action_types = ['select', 'update', 'insert', 'delete'];
$tables = ['albums', 'artists', 'audio_features', 'genres', 'r_albums_artists', 'r_albums_tracks', 'r_artist_genre', 'r_track_artist', 'tracks', 'test'];
$confusing_columns = ['id', 'name', 'popularity', 'duration'];
$table_columns = [
	'test' => ['ID', 'col'],
	'albums' => ['albums_id', 'albums_name', 'album_group', 'album_type', 'release_date', 'albums_popularity'],
	'artists' => ['artists_name', 'artists_id', 'artists_popularity', 'followers'],
	'audio_features' => ['audio_features_id', 'acousticness', 'analysis_url', 'danceability', 'audio_features_duration', 'energy', 'instrumentalness', 'key', 'liveness', 'loudness', 'mode', 'speechiness', 'tempo', 'time_signature', 'valence'],
	'genres' => ['genres'],
	'tracks' => ['tracks_id', 'disc_number', 'tracks_duration', 'explicit', 'audio_feature_id', 'tracks_name', 'preview_url', 'track_number', 'tracks_popularity', 'is_playable']
];
$text_columns = ['albums_name', 'album_group', 'album_type', 'artists_name', 'analysis_url', 'genres', 'tracks_name', 'preview_url'];
$id_tables = ['albums', 'artists', 'audio_features', 'tracks'];
$id_columns = ['albums_id', 'artists_id', 'audio_features_id', 'tracks_id'];
$foreign_id_columns = ['audio_feature_id'];
$int_columns = ['albums_popularity', 'artists_popularity', 'followers', 'audio_features_duration', 'key', 'mode', 'time_signature', 'disc_number', 'tracks_duration', 'explicit', 'track_number', 'tracks_popularity', 'is_playable'];
$double_columns = ['acousticness', 'danceability', 'energy', 'instrumentalness', 'liveness', 'loudness', 'speechiness', 'tempo', 'valence'];
$date_columns = ['release_date'];

$text_like_columns = array_merge($text_columns, $id_columns, $foreign_id_columns, $date_columns);
$number_like_columns = array_merge($int_columns, $double_columns, $date_columns);

$value_rules = [
	'albums_popularity' => [0,100],
	'artists_popularity' => [0,100],
	'followers' => [0, PHP_INT_MAX],
	'acousticness' => [0,1],
	'danceability' => [0,1],
	'energy' => [0,1],
	'instrumentalness' => [0,1],
	'liveness' => [0,1],
	'speechiness' => [0,1],
	'valence' => [0,1],
	'audio_features_duration' => [1, 2147483647],
	'key' => [0,127],
	'time_signature' => [0,127],
	'loudness' => [-100,100],
	'mode' => [-128,127],
	'tempo' => [0,PHP_FLOAT_MAX],
	'valence' => [0,PHP_FLOAT_MAX],
	'disc_number' => [1,127],
	'explicit' => [0,127],
	'track_number' => [1,32768],
	'tracks_popularity' => [0,100],
	'is_playable' => [0,1],
	'tracks_duration' => [1,2147483647]
];
?>