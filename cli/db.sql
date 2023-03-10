CREATE TABLE IF NOT EXISTS posts(
    post_id VARCHAR(255) NOT NULL PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    content VARCHAR(300) NOT NULL,
    thumbnail VARCHAR(50) NOT NULL,
    author VARCHAR(50) NOT NULL,
    posted_at DATETIME NOT NULL

);

CREATE TABLE IF NOT EXISTS categories(
    category_id VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(50)
);
-- posts_categories:
-- id_post
-- id_category

CREATE TABLE IF NOT EXISTS posts_categories(
    id_post VARCHAR(255) NOT NULL,
    id_category VARCHAR(255) NOT NULL,
    FOREIGN KEY(id_post) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY(id_category) REFERENCES categories(category_id) ON DELETE CASCADE,
    PRIMARY KEY(id_post, id_category)
);

